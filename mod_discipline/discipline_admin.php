<?php
/*
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
$titre_page = "Gestion du module Discipline";
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

if ((isset($_POST['is_posted']))&&(isset($_POST['activer']))) {
	check_token();
    if (!saveSetting("active_mod_discipline", $_POST['activer'])) {
		$msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation !";
	}

	$autorise_commentaires_mod_disc=isset($_POST['autorise_commentaires_mod_disc']) ? $_POST['autorise_commentaires_mod_disc'] : "no";
	if (!saveSetting("autorise_commentaires_mod_disc", $autorise_commentaires_mod_disc)) {
		$msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation \"autorise_commentaires_mod_disc\" !";
	}
}

if (isset($_POST['is_posted']) and ($msg=='')) {
  $msg = "Les modifications ont été enregistrées !";
  $post_reussi=TRUE;
}

// ====== Inclusion des balises head et du bandeau =====
include_once("../lib/header_template.inc.php");

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


$nom_gabarit = '../templates/'.$_SESSION['rep_gabarits'].'/mod_discipline/discipline_admin_template.php';

$tbs_last_connection=""; // On n'affiche pas les dernières connexions
include($nom_gabarit);


/*
?>
<p class=bold><a href="../accueil_modules.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>
<h2>Configuration générale</h2>
<i>La désactivation du module Discipline n'entraîne aucune suppression des données. Lorsque le module est désactivé, les utilisateurs n'ont pas accès au module.</i>
<br />
<form action="discipline_admin.php" name="form1" method="post">
<p>
<input type="radio" name="activer" id='activer_y' value="y" <?php if (getSettingValue("active_mod_discipline")=='y') echo " checked"; ?> />&nbsp;<label for='activer_y' style='cursor: pointer;'>Activer le module Discipline</label><br />
<input type="radio" name="activer" id='activer_n' value="n" <?php if (getSettingValue("active_mod_discipline")=='n') echo " checked"; ?> />&nbsp;<label for='activer_n' style='cursor: pointer;'>Désactiver le module Discipline</label>
</p>

<input type="hidden" name="is_posted" value="1" />
<center><input type="submit" value="Enregistrer" style="font-variant: small-caps;"/></center>
</form>
<?php
	echo "<p><br /></p>\n";
	require("../lib/footer.inc.php");
 *
 */
?>
