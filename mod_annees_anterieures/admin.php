<?php
/*
 * Last modification  : 22/07/2007
 *
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
};

// Check access
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$msg = '';
if (isset($_POST['activer'])) {
    if (!saveSetting("active_annees_anterieures", $_POST['activer'])) $msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation !";
}



if (isset($_POST['is_posted']) and ($msg=='')) $msg = "Les modifications ont été enregistrées !";
//**************** EN-TETE *******************************
// header
$titre_page = "Gestion du module Années antérieures";
require_once("../lib/header.inc");
//**************** EN-TETE *******************************

$quitter_la_page=isset($_GET['quitter_la_page']) ? $_GET['quitter_la_page'] : NULL;
if(!isset($quitter_la_page)){
	echo "<p class='bold'><a href='../accueil_modules.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>\n";
}
else {
	echo "<p class=bold><a href=\"#\"";
	echo " onclick=\"self.close();\"";
	echo ">Refermer la page</a></p>\n";
}

?>
<h2>Configuration générale</h2>
<i>La désactivation du module Années antérieures n'entraîne aucune suppression des données. Lorsque le module est désactivé, personne n'a accès au module.</i>
<br />
<form action="<?php
	echo $_SERVER['PHP_SELF'];
?>" name="form1" method="post">

<p>
<input type="radio" name="activer" id='activer_y' value="y" <?php if (getSettingValue("active_annees_anterieures")=='y') echo " checked"; ?> />&nbsp;<label for='activer_y' style='cursor: pointer;'>Activer le module Années antérieures</label><br />
<input type="radio" name="activer" id='activer_n' value="n" <?php if (getSettingValue("active_annees_anterieures")=='n') echo " checked"; ?> />&nbsp;<label for='activer_n' style='cursor: pointer;'>Désactiver le module Années antérieures</label>
</p>

<input type="hidden" name="is_posted" value="1" />
<center><input type="submit" value="Enregistrer" style="font-variant: small-caps;"/></center>
</form>
<?php require("../lib/footer.inc.php");?>
