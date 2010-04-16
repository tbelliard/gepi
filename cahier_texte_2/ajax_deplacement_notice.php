<?php
/*
 * $Id$
 *
 * Copyright 2009 Josselin Jacquard
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
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

// Dans le cas ou on poste une notice ou un devoir, pas de traitement anti_inject
// Pour ne pas interférer avec fckeditor
$traite_anti_inject = 'no';

require_once("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");

$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

//récupération des paramètres de la requète
$id_ct = isset($_POST["id_ct"]) ? $_POST["id_ct"] :(isset($_GET["id_ct"]) ? $_GET["id_ct"] :NULL);
$date_deplacement = isset($_POST["date_deplacement"]) ? $_POST["date_deplacement"] :(isset($_GET["date_deplacement"]) ? $_GET["date_duplication"] :NULL);
$id_groupe = isset($_POST["id_groupe_deplacement"]) ? $_POST["id_groupe_deplacement"] :(isset($_GET["id_groupe_deplacement"]) ? $_GET["id_groupe_deplacement"] :NULL);
$type = isset($_POST["type"]) ? $_POST["type"] :(isset($_GET["type"]) ? $_GET["type"] :NULL);

$ctCompteRendu = null;
if ($type == 'CahierTexteTravailAFaire') {
	$ctCompteRendu = CahierTexteTravailAFairePeer::retrieveByPK($id_ct);
} elseif ($type == 'CahierTexteCompteRendu') {
	$ctCompteRendu = CahierTexteCompteRenduPeer::retrieveByPK($id_ct);
} elseif ($type == 'CahierTexteNoticePrivee') {
	$ctCompteRendu = CahierTexteNoticePriveePeer::retrieveByPK($id_ct);
}

if ($ctCompteRendu == null) {
	echo ("Erreur deplacement de notice : Pas de notice trouvée.");
	die();
}
$groupe = GroupePeer::retrieveByPK($id_groupe);
if ($groupe == null) {
	echo ("Erreur deplacement de notice  : Pas de groupe spécifié");
	die;
}

$ctCompteRendu->setGroupe($groupe);
$ctCompteRendu->setDateCt($date_deplacement);

$ctCompteRendu->save();
$utilisateur->clearAllReferences();
echo("Deplacement effectué");
?>
