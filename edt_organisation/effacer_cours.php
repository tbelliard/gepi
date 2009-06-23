<?php
// PAS BON DU TOUT A REVOIR
/**
 * Fichier destiné à effacer une entrée dans l'emploi du temps
 * Il reçoit par GET un id de cours à supprimer
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

$titre_page = "Emploi du temps - Effacer un cours";
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

// INSERT INTO `gepi`.`droits` (`id` ,`administrateur` ,`professeur` ,`cpe` ,`scolarite` ,`eleve` ,`responsable` ,`secours` ,`description` ,`statut`) VALUES ('/edt_organisation/effacer_cours.php', 'V', 'V', 'F', 'F', 'F', 'F', 'F', 'Effacer un cours des EdT', '');
// Sécurité
if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}
// Sécurité supplémentaire par rapport aux paramètres du module EdT / Calendrier
if (param_edt($_SESSION["statut"]) != "yes") {
  if ($_SESSION["statut"] == "professeur" AND getSettingValue("edt_remplir_prof") == 'y'){
    // On autorise la lecture de cette page
  }else{
    Die('Vous devez demander à votre administrateur l\'autorisation de voir cette page.');
  }
}
// CSS et js particulier à l'EdT
$javascript_specifique = "edt_organisation/script/fonctions_edt";
$style_specifique = "edt_organisation/style_edt";

// On insère l'entête de Gepi
require_once("../lib/header.inc");

// On ajoute le menu EdT
require_once("./menu.inc.php"); ?>


<br />
<!-- la page du corps de l'EdT -->

	<div id="lecorps">

<?php
// Initialisation des variables
$supprimer_cours = isset($_GET["supprimer_cours"]) ? $_GET["supprimer_cours"] : NULL;
$type_edt = isset($_GET["type_edt"]) ? $_GET["type_edt"] : NULL;
$identite = isset($_GET["identite"]) ? $_GET["identite"] : NULL;

if ($_SESSION["statut"] == "professeur" AND getSettingValue("edt_remplir_prof") == 'y' AND strtolower($identite) != strtolower($_SESSION["login"])){
  Die("Vous ne pouvez pas effacer un cours d'un coll&egrave;gue");
}
$effacer_cours = mysql_query("DELETE FROM edt_cours WHERE id_cours = '".$supprimer_cours."'") OR die ('Impossible d\'effacer ce cours');

if (!$effacer_cours) {
	echo '<span class="refus">Revenez en arrière avec la flèche de votre navigateur et recommencez.</span>';
}
else {
	echo '
		<span class="accept">Ce cours est effacé !</span>
		<form name="retour" method="post" action="index_edt.php">
		<input type="hidden" name="visioedt" value="'.$type_edt.'1" />
		<input type="hidden" name="login_edt" value="'.$identite.'" />
		<input type="hidden" name="type_edt_2" value="'.$type_edt.'" />
		<input type="image" src="../images/icons/back.png" border="0" name="submit" alt="Revenir" title="Revenir" /> Revenir
		</form>
		';
}
?>

	</div>
<br />
<br />
<?php
// inclusion du footer
require("../lib/footer.inc.php");
?>