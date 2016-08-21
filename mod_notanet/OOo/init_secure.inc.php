<?php
/* $Id$ */
// include("../lib/initialisationsPropel.inc.php");
require_once("../../lib/initialisations.inc.php");


// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../../logout.php?auto=1");
	die();
};


//======================================================================================
// Section checkAccess() à décommenter en prenant soin d'ajouter le droit correspondant:
// Pour GEPI 1.4.3 à 1.4.4
// INSERT INTO droits VALUES('/mod_notanet/fiches_brevet.php','V','F','F','F','F','F','Accès aux fiches brevet','');
// Pour GEPI 1.5.x
// INSERT INTO droits VALUES('/mod_notanet/fiches_brevet.php','V','F','F','F','F','F','F','F','Accès à l export NOTANET','');
if (!checkAccess()) {
	header("Location: ../../logout.php?auto=1");
	die();
}
//======================================================================================





?>