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


// +++++++++++++++++++++ Code métier ++++++++++++++++++++++++++++
include("lib/erreurs.php");
include("activeRecordGepi.class.php");
include("classes/abs_creneaux.class.php");


try{

  $creneaux = new Abs_creneau();
  $liste_creneaux = $creneaux->findAll();

/*
  echo '<pre>';
  print_r($_POST);
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

  <table>
    <tr>
      <td>nom</td>
      <td>début</td>
      <td>fin</td>
      <td>type</td>
    </tr>

    <?php foreach($liste_creneaux as $aff_creneau): ?>

    <tr>
      <td><?php echo $aff_creneau->nom_creneau ; ?></td>
      <td><?php echo Abs_creneau::heureFr($aff_creneau->debut_creneau); ?></td>
      <td><?php echo Abs_creneau::heureFr($aff_creneau->fin_creneau); ?></td>
      <td><?php echo $aff_creneau->type_creneau ; ?></td>
    </tr>

    <?php endforeach; ?>

  </table>
  
  <p onclick="afficherDiv('id_nouveau')">Ajouter un nouveau cr&eacute;neau</p>
  <div id="id_nouveau" style="display: none;">

      <input type="text" id="nom" name="nom" value="" />
      <input type="text" id="deb" name="deb" value="" />
      <input type="text" id="fin" name="fin" value="" />
      <input type="text" id="type" name="type" value="" />
      <input type="hidden" id="action" name="action" value="enregistrer" />

      <input type="submit" name="enregistrer" value="Enregistrer" onclick="gestionaffAbs('aff_result', 'nom||deb||fin||type||action', 'parametrage_creneaux_ajax.php')" />
  </div>



</div>

