<?php

/**
 *
 * @version $Id$
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


$affiche_connexion = 'yes';
$niveau_arbo = 1;
	// Initialisations files
	require_once("../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
   header("Location: /mon_compte.php?change_mdp=yes");
   die();
} else if ($resultat_session == '0') {
   header("Location: ../logout.php?auto=1");
   die();
};
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
die();
}

// ============== les variables =========================
$autorise = isset($_POST["autorise"]) ? $_POST["autorise"] : 'n';
$action = isset($_POST["action"]) ? $_POST["action"] : NULL;
$aff_msg = NULL;

// ============== Le code métier ========================
if ($action == 'valide') {
	// On change le setting en question ou on le crée avec la fonction ...... qui gère tout
	$test = saveSetting('statuts_prives', $autorise);
	$decalage = ($autorise == 'y') ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' : NULL;
	$aff_msg = 'y';
}

// On détermine le selected
$selected = (getSettingValue('statuts_prives') == 'y') ? ' checked="checked"' : NULL;

// ++++++++++++++++++++++ ENTETE ++++++++++++++++++++++++
$titre_page = "Module statuts personnalisés";
require_once("../lib/header.inc");
// ++++++++++++++++++++ FIN ENTETE ++++++++++++++++++++++
?>

<p class="bold"><a href="../accueil_modules.php"><img src="../images/icons/back.png" alt="Retour" class="back_link" /> Retour</a></p>

<h3 class="gepi">Si Gepi d&eacute;finit plusieurs statuts par d&eacute;faut, il est possible d'en cr&eacute;er de nouveaux en passant par cet outil.</h3>

<form id="auth_statuts_perso" action="creer_statut_admin.php" method="post">
	<p>

		<input type="hidden" name="action" value="valide" />
		<input type="checkbox" id="idAutorise" name="autorise" value="y" onchange='document.getElementById("auth_statuts_perso").submit();' <?php echo $selected; ?>/>
		<label for="idAutorise">&nbsp;Autoriser la cr&eacute;ation de nouveaux statuts personnalis&eacute;s par l'admnistrateur.</label>

	</p>
	<?php if($aff_msg == 'y') { ?>

	<p style="color: green; font-weight: bold;"><?php echo $decalage; ?>La modification a bien été enregistrée.</p>

	<?php } ?>

</form>


<?php require_once("../lib/footer.inc.php"); ?>