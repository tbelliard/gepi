<?php
/*
 * @version: $Id: discipline_admin.php 2554 2008-10-12 14:49:29Z crob $
 *
 * Copyright 2001, 2009 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
}

// Check access
if (!checkAccess()) {
  header("Location: ../logout.php?auto=1");
  die();
}

$msg = '';
if ((isset($_POST['is_posted']))&&(isset($_POST['activer']))) {
    if (!saveSetting("active_mod_ects", $_POST['activer'])) $msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation !";
}

if (isset($_POST['is_posted']) and ($msg=='')) {$msg = "Les modifications ont été enregistrées !";}
// header
$titre_page = "Gestion du module ECTS";
require_once("../lib/header.inc");
?>
<p class=bold><a href="../accueil_modules.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>
<h2>Configuration générale</h2>
<i>La désactivation du module ECTS n'entraîne aucune suppression des données. Lorsque le module est désactivé, il n'est plus possible de gérer les valeurs d'ECTS pour les enseignements ou de saisir des ECTS pour les élèves.</i>

<br />
<form action="ects_admin.php" name="form1" method="post">
<p>
<input type="radio" name="activer" id='activer_y' value="y" <?php if (getSettingValue("active_mod_ects")=='y') echo " checked"; ?> />&nbsp;<label for='activer_y' style='cursor: pointer;'>Activer le module ECTS</label><br />
<input type="radio" name="activer" id='activer_n' value="n" <?php if (getSettingValue("active_mod_ects")=='n') echo " checked"; ?> />&nbsp;<label for='activer_n' style='cursor: pointer;'>Désactiver le module ECTS</label>
</p>

<input type="hidden" name="is_posted" value="1" />
<center><input type="submit" value="Enregistrer" style="font-variant: small-caps;"/></center>
</form>
<?php
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>