<?php

/*
 * Cette page permet de rentrer une modification
 * temporaire dans l'emploi du temps pour une semaine donn&eacute;e
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
	// Fichier en TRAVAIL
Die();

$titre_page = "Emploi du temps - Modifications temporaires";
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
$style_specifique = "templates/".NameTemplateEDT()."/css/style_edt";
// On insère l'entête de Gepi
require_once("../lib/header.inc");

// On ajoute le menu EdT
require_once("./menu.inc.php");
?>


<br />
<!-- la page du corps de l'EdT -->

	<div id="lecorps">

<?php

	// Initialisation des variables
$modifedt = isset($_GET["modifedt"]) ? $_GET["modifedt"] : (isset($_POST["modifedt"]) ? $_POST["modifedt"] : NULL);
$login_prof = isset($_POST["login_prof"]) ? $_POST["login_prof"] : NULL;
$login_classe = isset($_POST["login_classe"]) ? $_POST["login_classe"] : NULL;
$heure = isset($_POST["heure"]) ? $_POST["heure"] : NULL;
$jour_semaine = isset($_POST["jour_semaine"]) ? $_POST["jour_semaine"] : NULL;
$semaine = isset($_POST["semaine"]) ? $_POST["semaine"] : NULL;
$login_salle = isset($_POST["login_salle"]) ? $_POST["login_salle"] : NULL;

	// Début du formulaire

echo '
	<form action="index_edt.php" name="modifier" method="post">
		<input type="hidden" name="modifedt" value="tempo">
		<select  name="login_prof">
			<option name="rien">Professeur</option>
	';
	// Chois du professeur
	$tab_select = renvoie_liste("prof");

	for($i=0;$i<count($tab_select);$i++) {

	echo "
			<option value='".$tab_select[$i]["login"]."'>".$tab_select[$i]["nom"]." ".$tab_select[$i]["prenom"]."</option>\n";
	}
echo '
		</select>
		<select name="login_classe">
			<option name="rien">Classe</option>
	';
	// Choix de la classe
	$tab_select_classe = renvoie_liste("classe");

	for($c=0;$c<count($tab_select_classe);$c++) {

		echo "
			<option value='".$tab_select_classe[$c]["id"]."'>".$tab_select_classe[$c]["classe"]."</option>\n";
	}

	echo '
		</select>
	<br /><br />
		';

	// choix de l'horaire

	$req_heure = mysql_query("SELECT * FROM absences_creneaux ORDER BY heuredebut_definie_periode");
	$rep_heure = mysql_fetch_array($req_heure);

echo '
		<select name="heure">
			<option value="rien">Horaire</option>
	';
	$tab_select_heure = array();

	for($b=0;$b<count($rep_heure);$b++) {
		$tab_select_heure[$b]["id_heure"] = mysql_result($req_heure, $b, "id_definie_periode");
		$tab_select_heure[$b]["creneaux"] = mysql_result($req_heure, $b, "nom_definie_periode");
		$tab_select_heure[$b]["heure_debut"] = mysql_result($req_heure, $b, "heuredebut_definie_periode");
		$tab_select_heure[$b]["heure_fin"] = mysql_result($req_heure, $b, "heurefin_definie_periode");

		echo "
			<option value='".$tab_select_heure[$b]["id_heure"]."'>".$tab_select_heure[$b]["creneaux"]." : ".$tab_select_heure[$b]["heure_debut"]." - ".$tab_select_heure[$b]["heure_fin"]."</option>\n";

	}
echo '
		</select>
	';

	// choix du jour

	$req_jour = mysql_query("SELECT id_horaire_etablissement, jour_horaire_etablissement FROM horaires_etablissement");
	$rep_jour = mysql_fetch_array($req_jour);

echo '
		<select name="jour_semaine">
			<option value="rien">Jour</option>
	';
	$tab_select_jour = array();

	for($a=0;$a<=count($rep_jour);$a++) {
		$tab_select_jour[$a]["id"] = mysql_result($req_jour, $a, "id_horaire_etablissement");
		$tab_select_jour[$a]["jour_sem"] = mysql_result($req_jour, $a, "jour_horaire_etablissement");

		echo "
			<option value='".$tab_select_jour[$a]["id"]."'>".$tab_select_jour[$a]["jour_sem"]."</option>\n";
	}
echo '
		</select>
	<br /><br />
	';

	// choix de la semaine

	$req_semaine = mysql_query("SELECT * FROM edt_semaines");
	$rep_semaine = mysql_fetch_array($req_semaine);

echo '
		<select name="semaine">
			<option value="rien">Semaine</option>
	';
	$tab_select_semaine = array();

	for($d=0;$d<52;$d++) {
		$tab_select_semaine[$d]["id_semaine"] = mysql_result($req_semaine, $d, "id_edt_semaine");
		$tab_select_semaine[$d]["num_semaine"] = mysql_result($req_semaine, $d, "num_edt_semaine");
		$tab_select_semaine[$d]["type_semaine"] = mysql_result($req_semaine, $d, "type_edt_semaine");

		echo "
			<option value='".$tab_select_semaine[$d]["id_semaine"]."'>Semaine n° ".$tab_select_semaine[$d]["num_semaine"]." (".$tab_select_semaine[$d]["type_semaine"].") </option>\n";

	}
echo '
		</select>
		<select  name="login_salle">
			<option value="rien">Salle</option>
	';
	// CHoix de la salle
	$tab_select = renvoie_liste("salle");

	for($i=0;$i<count($tab_select);$i++) {

		echo "
			<option value='".$tab_select[$i]["id_salle"]."'>".$tab_select[$i]["nom_salle"]."</option>\n";
	}
echo '
		</select>
		<br /><br />
		<input type="submit" name="Valider" value="Valider" />
		<br /><br />
	</form>
	';

?>

	</div>
<br />
<br />
<?php
// inclusion du footer
require("../lib/footer.inc.php");
?>