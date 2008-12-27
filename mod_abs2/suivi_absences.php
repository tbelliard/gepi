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


// ============== Code métier ===============================
include("classes/absences.class.php");
include("lib/erreurs.php");
include("helpers/aff_listes_utilisateurs.inc.php");
include 'classes/activeRecordGepi.class.php';
include 'classes/abs_informations.class.php';


try{

  $_absences = new Abs_information();
  $liste_absents = $_absences->findAll(array('order_by' => 'debut_abs'));

}catch(exception $e){
  affExceptions($e);
}
//**************** EN-TETE *****************
$titre_page = "Les absences";
require_once("../lib/header.inc");
require("lib/abs_menu.php");
//**************** FIN EN-TETE *****************


?>
<table>
  <tr>
    <th>Saisisseur (sic)</th>
    <th>Eleve ?</th>
    <th>Quand saisie ?</th>
    <th>creneau Deb</th>
    <th>Creneau fin</th>
  </tr>
  <?php foreach($liste_absents as $aff_liste_absents): ?>

  <tr>
    <td><?php echo $aff_liste_absents->utilisateurs_id ; ?></td>
    <td><?php echo $aff_liste_absents->eleves_id ; ?></td>
    <td><?php echo $aff_liste_absents->date_saisie ; ?></td>
    <td><?php echo $aff_liste_absents->debut_abs ; ?></td>
    <td><?php echo $aff_liste_absents->fin_abs ; ?></td>
  </tr>

  <?php endforeach; ?>

</table>




<?php require_once("../lib/footer.inc.php"); ?>