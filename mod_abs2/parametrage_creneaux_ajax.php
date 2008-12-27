<?php
/**
 *
 *
 * @version $Id: parametrage_ajax.php 2708 2008-11-28 21:37:48Z jjocal $
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

// ==================== VARIABLES ===============================
$msg = NULL;
$_id  = isset($_POST["_id"]) ? $_POST["_id"] : NULL;
$type = isset($_POST["type"]) ? $_POST["type"] : NULL;

// +++++++++++++++++++++ Code métier ++++++++++++++++++++++++++++
include("lib/erreurs.php");
include("classes/activeRecordGepi.class.php");
include("classes/abs_creneaux.class.php");


try{

  // On commence par analyser les variables envoyées par la requête AJAX
  $tester_type = explode("||", $type);

  if (isset ($tester_type[4]) AND $tester_type[4] == 'action' AND $tester_type[1] != 'action2') {
    // Alors on teste $_id qui doit ressembler à $type
    $tester_id = explode("||", $_id);

    if (isset ($tester_id[4]) AND $tester_id[4] == "enregistrer"){
      // On vérifie les informations envoyées avant de sauvegarder

      $new_creneau = new Abs_creneau();

      if ($deb = $new_creneau->heureBdd($tester_id[1]) AND $fin = $new_creneau->heureBdd($tester_id[2]) AND $tester_id[0] != '') {
        $new_creneau->setChamp('nom_creneau', $tester_id[0]);
        $new_creneau->setChamp('debut_creneau', $deb);
        $new_creneau->setChamp('fin_creneau', $fin);
        $new_creneau->setChamp('type_creneau', $tester_id[3]);
        if ($new_creneau->save()){
          $msg = '<p style="color: green;">Le nouveau cr&eacute;neau est enregistr&eacute</p>';
        }else{
          throw new Exception("Impossible d'enregistrer ce nouveau créneau. || " . 'pas de requête à afficher');
        }
        
      }else{
        $msg = '<p style="color: red;">Il manque des informations ou certaines informations sont incompl&egrave;tes.</p>';
      }


    }
  }elseif($tester_type[1] == 'action2'){
    // Dans ce cas, l'utilisateur demande à effacer un créneau
    $tester_id = explode("||", $_id);
      if ($tester_id[1] == 'effacer'){
        $del_creneau = new Abs_creneau();
        $del_creneau->_delete($tester_id[0]); // et c'est effacé

      }

  }


  $creneaux = new Abs_creneau();
  $liste_creneaux = $creneaux->findAll(array('order_by' => 'debut_creneau'));

/*
  echo '<pre>';
  print_r($tester_type);
  echo '</pre>';
*/


}catch(exception $e){
  // Cette fonction est présente dans /lib/erreurs.php
  affExceptions($e);
}
// On précise l'entête HTML pour que le navigateur ne se perde pas .
header('Content-Type: text/html; charset:utf-8');

?>
<div id="ajouter_creneau">

  <table style="margin-left: 50px; padding: 5px 10px 5px 10px; border: solid 2px grey;">
    <tr style="width: 50px;">
      <td>nom</td>
      <td>d&eacute;but</td>
      <td>fin</td>
      <td>type</td>
    </tr>

    <?php foreach($liste_creneaux as $aff_creneau): ?>

    <tr>
      <td><?php echo $aff_creneau->nom_creneau ; ?></td>
      <td><?php echo Abs_creneau::heureFr($aff_creneau->debut_creneau); ?></td>
      <td><?php echo Abs_creneau::heureFr($aff_creneau->fin_creneau); ?></td>
      <td><?php echo $aff_creneau->type_creneau ; ?></td>
      <td>
        <input type="hidden" name="del<?php echo $aff_creneau->id; ?>" id="del<?php echo $aff_creneau->id; ?>" value="<?php echo $aff_creneau->id; ?>" />
        <img src="../images/icons/delete.png" alt="effacer" title="Effacer" onclick="gestionaffAbs('aff_result', 'del<?php echo $aff_creneau->id; ?>||action2', 'parametrage_creneaux_ajax.php');" /></td>
    </tr>

    <?php endforeach; ?>
    <input type="hidden" id="action2" name="action2" value="effacer" />
  </table>
  
  <p onclick="afficherDiv('id_nouveau')" class="lienWeb">Ajouter un nouveau cr&eacute;neau</p>
  <div id="id_nouveau" style="display: none; margin-top: 20px; border: solid 2px blue; padding: 10px 10px 10px 10px; width: 500px; text-align: right;">

      <p>
        <label for="nom">Nom du cr&eacute;neau</label>
        <input type="text" id="nom" name="nom" value="" />
      </p>
      <p>
        <label for="deb">Heure d&eacute;but (hh:mm)</label>
        <input type="text" size="5" id="deb" name="deb" value="" />
      </p>
      <p>
        <label for="fin">Heure de fin (hh:mm)</label>
        <input type="text" size="5" id="fin" name="fin" value="" />
      </p>
      <p>
        <label for="type">Type de cr&eacute;neau</label>
        <select id="type" name="type">
          <option value="cours" selected="selected">Cours</option>
          <option value="repas">Repas</option>
          <option value="pause">Pause</option>
        </select>
      </p>
      <input type="hidden" id="action" name="action" value="enregistrer" />

      <input type="submit" name="enregistrer" value="Enregistrer" onclick="gestionaffAbs('aff_result', 'nom||deb||fin||type||action', 'parametrage_creneaux_ajax.php');" />
  </div>

<?php echo $msg; ?>

</div>

