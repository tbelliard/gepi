<?php

/**
 *
 * @version $Id: creer_statut_admin.php 5951 2010-11-22 17:05:48Z crob $
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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

$accessibilite="y";
$titre_page = "Module statuts personnalisés";
$gepiPathJava="./..";
$post_reussi=FALSE;
$msg = '';
$affiche_connexion = 'no';
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
}
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
	check_token();
	// On change le setting en question ou on le crée avec la fonction ...... qui gère tout
  /*
	$test = saveSetting('statuts_prives', $autorise);
	$decalage = ($autorise == 'y') ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' : NULL;
	$aff_msg = 'y';
   */
  if ($autorise!=getSettingValue('statuts_prives')){
	if  (saveSetting('statuts_prives', $autorise)){
	  $msg="La modification a bien été enregistrée.";
	  $post_reussi=TRUE;
	} else {
	  $msg="Échec lors de l'enregistrement de la modification.";

	}
  }
}

// On détermine le selected
//$selected = (getSettingValue('statuts_prives') == 'y') ? ' checked="checked"' : NULL;

// ++++++++++++++++++++++ ENTETE ++++++++++++++++++++++++
//$titre_page = "Module statuts personnalisés";
//require_once("../lib/header.inc");
// ++++++++++++++++++++ FIN ENTETE ++++++++++++++++++++++


// ====== Inclusion des balises head et du bandeau =====
include_once("../lib/header_template.inc");

if (!suivi_ariane($_SERVER['PHP_SELF'],$titre_page))
		echo "erreur lors de la création du fil d'ariane";
/****************************************************************
			FIN HAUT DE PAGE
****************************************************************/



/****************************************************************
			BAS DE PAGE
****************************************************************/
$tbs_microtime	="";
$tbs_pmv="";
require_once ("../lib/footer_template.inc.php");

/****************************************************************
			On s'assure que le nom du gabarit est bien renseigné
****************************************************************/
if ((!isset($_SESSION['rep_gabarits'])) || (empty($_SESSION['rep_gabarits']))) {
	$_SESSION['rep_gabarits']="origine";
}

//==================================
// Décommenter la ligne ci-dessous pour afficher les variables $_GET, $_POST, $_SESSION et $_SERVER pour DEBUG:
// $affiche_debug=debug_var();


$nom_gabarit = '../templates/'.$_SESSION['rep_gabarits'].'/utilisateurs/creer_statut_template.php';

$tbs_last_connection=""; // On n'affiche pas les dernières connexions
include($nom_gabarit);



/*
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


<?php require_once("../lib/footer.inc.php"); */ ?>