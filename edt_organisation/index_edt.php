<?php

/**
 * Fichier de gestion de l'emploi du temps dans Gepi version 1.5.x
 *
 * index_edt.php
 *
 * Copyright 2001, 2008 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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
require_once("./fonctions_edt.php");

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
	Die('Vous devez demander à votre administrateur l\'autorisation de voir cette page.');
}
// CSS et js particulier à l'EdT
$javascript_specifique = "edt_organisation/script/fonctions_edt";
$style_specifique = "edt_organisation/style_edt";

// On insère l'entête de Gepi
require_once("../lib/header.inc");

// On ajoute le menu EdT
require_once("./menu.inc.php");


// Pour revenir proprement, on crée le $_SESSION["retour"]
$_SESSION["retour"] = "index_edt";
?>


<br />
<!-- la page du corps de l'EdT -->

	<div id="lecorps">

<?php
if (isset($page_inc_edt) AND $page_inc_edt !== "") {
	include($page_inc_edt);
}
?>

	</div>
<br />
<br />
<?php
// inclusion du footer
require("../lib/footer.inc.php");
?>
