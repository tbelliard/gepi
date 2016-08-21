<?php

/**
 * Fichier de gestion de l'emploi du temps dans Gepi version 1.5.x
 *
 * @package		GEPI
 * @subpackage	EmploiDuTemps
 * @copyright	Copyright 2001, 2010 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal, Pascal Fautrero
 * @license		GNU/GPL, see COPYING.txt
 * 
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

// Initialisations files
require_once("../lib/initialisations.inc.php");

// fonctions edt
require_once('./choix_langue.php');
require_once("./fonctions_edt.php");            // --- fonctions de base communes à tous les emplois du temps
require_once("./fonctions_edt_prof.php");       // --- edt prof
require_once("./fonctions_edt_classe.php");     // --- edt classe
require_once("./fonctions_edt_salle.php");      // --- edt salle
require_once("./fonctions_edt_eleve.php");      // --- edt eleve
require_once("./fonctions_calendrier.php");
require_once("./fonctions_affichage.php");
require_once("./req_database.php");
// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

// Sécurité
if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}

// Sécurité supplémentaire par rapport aux paramètres du module EdT / Calendrier
if (param_edt($_SESSION["statut"]) != "yes") {
	Die(ASK_AUTHORIZATION_TO_ADMIN);
}

$mode_infobulle=isset($_POST['mode_infobulle']) ? $_POST['mode_infobulle'] : (isset($_GET['mode_infobulle']) ? $_GET['mode_infobulle'] : "n");
if($mode_infobulle=="n") {
	// CSS et js particulier à l'EdT
	$javascript_specifique = "edt_organisation/script/fonctions_edt";
	$ua = getenv("HTTP_USER_AGENT");
	if (strstr($ua, "MSIE 6.0")) {
		$style_specifique[] = "templates/".NameTemplateEDT()."/css/style_edt_ie6";
	}
	else {
		$style_specifique[] = "templates/".NameTemplateEDT()."/css/style_edt";
	}
}

//ob_start( 'ob_gzhandler' );

$visioedt=isset($_GET['visioedt']) ? $_GET['visioedt'] : (isset($_POST['visioedt']) ? $_POST['visioedt'] : NULL);
$salleslibres=isset($_GET['salleslibres']) ? $_GET['salleslibres'] : (isset($_POST['salleslibres']) ? $_POST['salleslibres'] : NULL);

// Pour revenir proprement, on crée le $_SESSION["retour"]
$_SESSION["retour"] = "index_edt";

VerifierTablesDelestage();

//debug_var();

    if ($salleslibres == "ok") {
        include('edt_chercher.php');
    }
    else {
        include('voir_edt.php');
    }

?>





