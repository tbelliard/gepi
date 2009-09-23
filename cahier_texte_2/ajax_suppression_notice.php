<?php
/*
 * $Id: index.php 2356 2008-09-05 14:02:27Z jjocal $
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Gabriel Fischer
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


header('Content-Type: text/html; charset=ISO-8859-1');
// On désamorce une tentative de contournement du traitement anti-injection lorsque register_globals=on
if (isset($_GET['traite_anti_inject']) OR isset($_POST['traite_anti_inject'])) $traite_anti_inject = "yes";
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

//On vérifie si le module est activé
if (getSettingValue("active_cahiers_texte")!='y') {
    die("Le module n'est pas activé.");
}

// Vérification : est-ce que l'utilisateur a le droit de supprimer cette entré ?
//$utilisateur = new Utilisateur();
$utilisateur = $_SESSION['utilisateurProfessionnel'];
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
    die();
}

//récupération de la notice
$id_objet = isset($_POST["id_objet"]) ? $_POST["id_objet"] :(isset($_GET["id_objet"]) ? $_GET["id_objet"] :NULL);
$type = isset($_POST["type"]) ? $_POST["type"] :(isset($_GET["type"]) ? $_GET["type"] :NULL);
echo $id_objet."; ";
echo $type."; ";
$objet = null;
if ($type == 'CahierTexteTravailAFaire') {
	$objet = CahierTexteTravailAFairePeer::retrieveByPK($id_objet);
} elseif ($type == 'CahierTexteCompteRendu') {
	$objet = CahierTexteCompteRenduPeer::retrieveByPK($id_objet);
} elseif ($type == 'CahierTexteCompteRenduFichierJoint') {
	$objet = CahierTexteCompteRenduFichierJointPeer::retrieveByPK($id_objet);
} elseif ($type == 'CahierTexteTravailAFaireFichierJoint') {
	$objet = CahierTexteTravailAFaireFichierJointPeer::retrieveByPK($id_objet);
} elseif ($type == 'CahierTexteNoticePrivee') {
	$objet = CahierTexteNoticePriveePeer::retrieveByPK($id_objet);
}

//si pas d'objet trouve, erreur du script
if ($objet == null) {
  echo("Erreur : pas d'objet trouvé.");
  die();
}

$objet->delete();
$utilisateur->clearAllReferences();
?>