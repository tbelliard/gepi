<?php
/*
 * $Id: inscription_admin.php 5951 2010-11-22 17:05:48Z crob $
 *
 * Copyright 2001-2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
$titre_page = "Gestion du module Notanet/Fiches Brevet";
$niveau_arbo = 1;
$gepiPathJava="./..";
$post_reussi=FALSE;
$msg = '';


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

// Check access
if (!checkAccess()) {
   header("Location: ../logout.php?auto=1");
   die();
}


if (isset($_POST['activer'])) {
	check_token();
    if (!saveSetting("active_inscription", $_POST['activer'])){
		$msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation !";
	}
	else{
		$msg = "Les modifications ont été enregistrées !";
		$post_reussi=TRUE;
	}
}


// header
// $titre_page = "Gestion du module Inscription";
// require_once("../lib/header.inc");


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


$nom_gabarit = '../templates/'.$_SESSION['rep_gabarits'].'/mod_inscription/inscription_admin_template.php';

$tbs_last_connection=""; // On n'affiche pas les dernières connexions
include($nom_gabarit);



/*
?>
<p class='bold'><a href="../accueil_modules.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>

<h2>Activation du module Inscription</h2>

<form action="inscription_admin.php" name="form1" method="post">
<p>Le module Inscription vous permet de définir un ou plusieurs items (stage, intervention, ...),
au(x)quel(s) les utilisateurs pourront s'inscrire ou se désinscrire en cochant ou décochant une croix.<br />
<a href="javascript:centrerpopup('help.php',800,500,'scrollbars=yes,statusbar=no,resizable=yes')">Consultez l'aide</a> pour en savoir plus.
<br /><br />
<label for='activer_y' style='cursor: pointer;'><input type="radio" name="activer" id="activer_y" value="y" <?php if (getSettingValue("active_inscription")=='y') echo " checked"; ?> />
&nbsp;Activer l'accès au module Inscription</label><br />
<label for='activer_n' style='cursor: pointer;'><input type="radio" name="activer" id="activer_n" value="n" <?php if (getSettingValue("active_inscription")=='n') echo " checked"; ?> />
&nbsp;Désactiver l'accès au module Inscription</label></p>

<input type="hidden" name="is_posted" value="1" />

<br />
<br />
<center><input type="submit" value="Enregistrer" style="font-variant: small-caps;" /></center>
</form>

<?php require("../lib/footer.inc.php"); */?>