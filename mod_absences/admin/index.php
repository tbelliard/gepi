<?php
/*
*
*$Id$
*
 * Copyright 2001, 2002 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Christian Chapel
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

$niveau_arbo = 2;
// Initialisations files
require_once("../../lib/initialisations.inc.php");
//mes fonctions
include("../lib/functions.php");

// Resume session
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
    header("Location: ../../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../../logout.php?auto=1");
    die();
};

// Check access
if (!checkAccess()) {
    header("Location: ../../logout.php?auto=1");
    die();
}

$msg = '';
if (isset($_POST['activer'])) {
    if (!saveSetting("active_module_absence", $_POST['activer'])) $msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation !";
}
if (isset($_POST['activer_prof'])) {
    if (!saveSetting("active_module_absence_professeur", $_POST['activer_prof'])) $msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation !";
}

if (isset($_POST['is_posted']) and ($msg=='')) $msg = "Les modifications ont été enregistrées !";

// header
$titre_page = "Gestion du module absence";
require_once("../../lib/header.inc");


echo "<p class=bold><a href=\"../../accueil.php\"><img src='../../images/icons/back.png' alt='Retour' class='back_link'/> Retour à l'accueil</a> | ";
echo "<a href=\"../../accueil_modules.php\">Retour administration des modules</a>";
echo "</p>";
?>
<H2>Gestion des absences par les CPE</H2>
<i>La désactivation du module de la gestion des absences n'entraîne aucune suppression des données. Lorsque le module est désactivé, les CPE n'ont pas accès au module.</i>
<br />
<form action="index.php" name="form1" method="post">
<input type="radio" name="activer" value="y" <?php if (getSettingValue("active_module_absence")=='y') echo " checked"; ?> />&nbsp;Activer le module de la gestion des absences<br />
<input type="radio" name="activer" value="n" <?php if (getSettingValue("active_module_absence")=='n') echo " checked"; ?> />&nbsp;Désactiver le module de la gestion des absences
<input type="hidden" name="is_posted" value="1" />
<br />
<i>La désactivation du module de la gestion des absences n'entraîne aucune suppression des données saisies par les professeurs. Lorsque le module est désactivé, les professeurs n'ont pas accès au module.
Normalement, ce module ne devrait être activé que si le module ci-dessus est lui-même activé.</i>
<H2>Saisie des absences par les professeurs</H2>
<input type="radio" name="activer_prof" value="y" <?php if (getSettingValue("active_module_absence_professeur")=='y') echo " checked"; ?> />&nbsp;Activer le module de la saisie des absences par les professeurs<br />
<input type="radio" name="activer_prof" value="n" <?php if (getSettingValue("active_module_absence_professeur")=='n') echo " checked"; ?> />&nbsp;Désactiver le module de la saisie des absences par les professeurs
<input type="hidden" name="is_posted" value="1" />
<div class="centre"><input type="submit" value="Enregistrer" style="font-variant: small-caps;"/></div>
</form>
<H2>Configuration avancée</H2>
<blockquote>
  <a href="admin_horaire_ouverture.php?action=visualiser">Définir les horaires d'ouverture de l'établissement</a><br />
  <a href="admin_periodes_absences.php?action=visualiser">Définir les créneaux horaires</a><br />
  <a href="admin_config_semaines.php?action=visualiser">Définir les types de semaine</a><br />
  <a href="admin_motifs_absences.php?action=visualiser">Définir les motifs des absences</a><br />
  <a href="admin_actions_absences.php?action=visualiser">Définir les actions sur le suivi des élèves</a>
</blockquote>
</body>
</html>
