<?php
/**
 *
 * @version $Id$
 *
 * Copyright 2010 Josselin Jacquard
 *
 * This file and the mod_abs2 module is distributed under GPL version 3, or
 * (at your option) any later version.
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
require_once("../lib/initialisationsPropel.inc.php");
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

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

//recherche de l'utilisateur avec propel
$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

//récupération des paramètres de la requète
$id_mef = isset($_POST["id_mef"]) ? $_POST["id_mef"] :NULL;
$total_eleves = isset($_POST["total_eleves"]) ? $_POST["total_eleves"] :(isset($_GET["total_eleves"]) ? $_GET["total_eleves"] :0);

$message_enregistrement = "";

$mef = null;
if ($id_mef != null && $id_mef != -1) {
    $mef = MefQuery::create()->findPk($id_mef);
    if ($mef == null) {
	$message_enregistrement .= "Probleme avec l'id du mef : ".$_POST['$id_mef']."<br/>";
    }
}

for($i=0; $i<$total_eleves; $i++) {

    //$id_eleve = $_POST['id_eleve_absent'][$i];

    //on test si l'eleve est enregistré absent
    if (!isset($_POST['active_mef_eleve'][$i])) {
	continue;
    }
    
    $eleve = EleveQuery::create()->findPk($_POST['active_mef_eleve'][$i]);
    if ($eleve == null) {
	$message_enregistrement .= "Probleme avec l'id eleve : ".$_POST['id_eleve_mef'][$i]."<br/>";
	continue;
    }
    $eleve->setMef($mef);
    $eleve->save();
    $message_enregistrement .= "Mef enregistré pour l'eleve : ".$eleve->getNom()."<br/>";
}

include("associer_eleve_mef.php");
?>