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
}

// ==================== VARIABLES ===============================
$type_req = isset($_POST["type"]) ? $_POST["type"] : NULL;
$_table = $_champ = NULL;
$_id = isset($_POST["_id"]) ? $_POST["_id"] : NULL;
$prefix = 'abs_';

// +++++++++++++++++++++ Code métier ++++++++++++++++++++++++++++
include("lib/erreurs.php");
include("classes/abs_gestion.class.php");

try{
  switch($type_req){
    case 'types':
    $_table = $prefix . $type_req;
    $_champ = 'type_absence';
      break;
    case 'motifs':
    $_table = $prefix . $type_req;
    $_champ = 'motifs';
      break;
    case 'actions':
    $_table = $prefix . $type_req;
    $_champ = 'type_action';
      break;
    case 'justifications':
    $_table = $prefix . $type_req;
    $_champ = 'type_justification';
      break;
    default:
      $_table = $_champ = NULL;
  } // switch

  $test = new abs_gestion();
  $test->getChamps($_champ);
  $test->getTable($_table);
  $aff = $test->voirTout();

}catch(exception $e){
  // Cette fonction est présente dans /lib/erreurs.php
  affExceptions($e);
}
// On précise l'entête HTML pour que le navigateur ne se perde pas .
header('Content-Type: text/html; charset:utf-8');



  // Cas où l'utilisateur vient de choisir ce qu'il veut afficher : types, actions, motifs et justifications

  echo '

  <p onclick="afficherDiv(\'ajouterEntree\');" class="lienWeb">Ajouter des ' .$type_req . '</p>

<div id="ajouterEntree" style="display: none;">Ajouter une entr&eacute;e dans la base

  <form action="#" method="post" id="testForm">

    <label for="' . $type_req . '">Entrez un ' . str_replace('s', '', $type_req) . ' :</label>
    <input type="text" id="' . $type_req . '" name="nom" value="" />
    <p onclick="gestionaffAbs(\'aff_result\', \'' . $type_req . '\', \'parametrage_ajax.php\');" class="lienWeb">Enregistrer</p>

  </form>

  <div id="reponse"></div>

</div>
  ';

if ($type_req != $_id) {{
  // Cas où l'utilisateur veut ajouter une entrée dans la base

  echo '&ccedil;a marche un peu : ' . utf8_encode($_id);

}

?>








