<?php

/**
 * @version $Id$
 *
 * Fichier de paramétrage de l'interface professeur pour la saisie des absences
 *
 * @copyright 2008
 *
 *  * This file is part of GEPI.
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
// les fonctions du module absences
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
// ===================== Initialisation des variables =======
$message = '';
$clic = isset($_POST["clic"]) ? $_POST["clic"] : NULL;
$date_phase1 = isset($_POST["date_phase1"]) ? $_POST["date_phase1"] : "n";
//$ = isset($_POST[""]) ? $_POST[""] : "n";
// ===================== Fin de l'initialisation ============

// Traitement des réglages
if ($clic == "ok") {
	$query = mysql_query("SELECT value FROM setting WHERE name = 'date_phase1'");
	$verif = mysql_num_rows($query);
	$rep_phase1 = mysql_result($query, 0, "value");
		// Si le setting n'existe pas, on le crée
	if ($verif == 0) {
		$creationSetting = mysql_query("INSERT INTO setting (name, value) values ('date_phase1', '".$date_phase1."')");
	}
	if ($rep_phase1 == $date_phase1) {
	// On ne fait rien
	}else {
		$modif = mysql_query("UPDATE setting SET value = '".$date_phase1."' WHERE name = 'date_phase1'");
		if (!$modif) {
			$message = "<p style=\"color: red;\">Une erreur est survenue lors de l'enregistrement !</p>";
		}else{
			$message = "<p style=\"color: green;\">La modification est bien enregistrée</p>";
		}
	}
}

$settingDate = getSettingValue("date_phase1");
//echo $settingDate." | ".$date_phase1." | ".$rep_phase1;
if (isset($settingDate) AND $settingDate == "y") {
	$checkeddate = ' checked="checked"';
}else{
	$checkeddate = '';
}

// ===================== Header et ses réglages =============
$titre_page = "L'interface de saisie des absences";
require_once("../../lib/header.inc");
// ===================== fin du header ======================
// On décide de l'affichage des checked
?>
<p class="bold">
	<a href="./index.php">
		<img src="../../images/icons/back.png" alt="Retour" class="back_link" />
		 Retour
	</a>
</p>
<h2>Param&eacute;trage de l'affichage de l'interface</h2>

<form name="interface_prof" action="interface_abs.php" method="post">
	<fieldset id="datesCreneaux">
		<legend>Dates et cr&eacute;neaux</legend>
			<p>Le professeur peut :</p>
			<p>
				<input type="checkbox" id="datePhase1" name="date_phase1" value="y"<?php echo $checkeddate; ?> />
				<label for="datePhase1">Modifier la date de saisie des absences.</label>
			</p>
	</fieldset>
	<input type="hidden" name="clic" value="ok" />
	<input type="submit" name="valider" value="Enregistrer" />

</form>
<?php echo $message;
// le footer
require_once("../../lib/footer.inc.php")
?>