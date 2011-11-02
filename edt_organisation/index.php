<?php

/**
 * Module EDT
 *
 * @version $Id: $
 *
 * Copyright 2011 Pascal Fautrero
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

$titre_page = "Emploi du temps";
$affiche_connexion = 'yes';
$niveau_arbo = 1;

include("../lib/initialisations.inc.php");
include("./lib/controller/frontcontroller.class.php");

// ===============================================================
//
//                       TESTS DE SECURITE
//
// ===============================================================
// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

// ===============================================================
//
//                 Préparer les scripts, css, entête
//
// ===============================================================
$action = isset($_GET["action"]) ? $_GET["action"] : NULL;
if ($action != "ajaxrequest") {
	$ua = getenv("HTTP_USER_AGENT");
	if (strstr($ua, "MSIE 6.0")) {
		$style_specifique[] = "edt_organisation/lib/template/css/style_ie6";
		$style_specifique[] = "templates/DefaultEDT/css/style_edt_ie6";
	}
	else if (strstr($ua, "MSIE 7")) {
		$style_specifique[] = "edt_organisation/lib/template/css/style_ie7";
	}
	$style_specifique[] = "edt_organisation/lib/template/css/style";
	$style_specifique[] = "templates/DefaultEDT/css/style_edt";

	$javascript_specifique[] = "edt_organisation/lib/template/js/script";
	$utilisation_scriptaculous = 'ok';
	$scriptaculous_effet="effects,dragdrop";

	
	
	require_once("../lib/header.inc");
	// ===============================================================
	//
	//                       DEMARRER LE CONTROLLEUR
	//
	// ===============================================================

	$front = frontController::getInstance()->dispatch();
	require("../lib/footer.inc.php");
}
else {
	$front = frontController::getInstance()->dispatch();
}


?>
