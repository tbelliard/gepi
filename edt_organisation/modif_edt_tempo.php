<?php

/**
 * Fichier pour modifier un EdT
 *
 * @version $Id$
 * @copyright 2007
 */

	// Initialisation des variables
$modifedt = isset($_GET["modifedt"]) ? $_GET["modifedt"] : (isset($_POST["modifedt"]) ? $_POST["modifedt"] : NULL);
$login_prof = isset($_POST["login_prof"]) ? $_POST["login_prof"] : NULL;
$login_classe = isset($_POST["login_classe"]) ? $_POST["login_classe"] : NULL;
$heure = isset($_POST["heure"]) ? $_POST["heure"] : NULL;
$jour_semaine = isset($_POST["jour_semaine"]) ? $_POST["jour_semaine"] : NULL;
$semaine = isset($_POST["semaine"]) ? $_POST["semaine"] : NULL;
$login_salle = isset($_POST["login_salle"]) ? $_POST["login_salle"] : NULL;

	// Pour éviter d'appeler ce fichier sans passer par edt_index.php
if ((isset($_GET["modifedt"]) OR isset($_POST["modifedt"])) AND $modifedt == "tempo") {
}
else {
	die ('Vous ne pouvez pas accéder à la modification de l\'EdT');
}

	// Début du formulaire

echo "<form action=\"index_edt.php\" name=\"modifier\" method=\"POST\">\n";
echo "<INPUT type=\"hidden\" name=\"modifedt\" value=\"tempo\">\n";

	// choix du prof
echo "<SELECT  name=\"login_prof\">\n";
	echo "<OPTION name=\"rien\">Professeur</OPTION>\n";

	$tab_select = renvoie_liste("prof");

	for($i=0;$i<count($tab_select);$i++) {

	echo ("<OPTION value='".$tab_select[$i]["login"]."'>".$tab_select[$i]["nom"]." ".$tab_select[$i]["prenom"]."</OPTION>\n");
	}
echo "</SELECT>\n";

	// Choix de la classe
echo "<SELECT name=\"login_classe\">\n";
echo "<OPTION name=\"rien\">Classe</OPTION>\n";

	$tab_select_classe = renvoie_liste("classe");

	for($c=0;$c<count($tab_select_classe);$c++) {

		echo ("<OPTION value='".$tab_select_classe[$c]["id"]."'>".$tab_select_classe[$c]["classe"]."</OPTION>\n");
	}

	echo "</SELECT>\n";
	echo "<br />\n<br />\n";

	// choix de l'horaire

	$req_heure = mysql_query("SELECT * FROM absences_creneaux ORDER BY heuredebut_definie_periode");
	$rep_heure = mysql_fetch_array($req_heure);

echo "<SELECT name=\"heure\">\n";
echo ("<OPTION value=\"rien\">Horaire</OPTION>\n");
	$tab_select_heure = array();

	for($b=0;$b<count($rep_heure);$b++) {
		$tab_select_heure[$b]["id_heure"] = mysql_result($req_heure, $b, "id_definie_periode");
		$tab_select_heure[$b]["creneaux"] = mysql_result($req_heure, $b, "nom_definie_periode");
		$tab_select_heure[$b]["heure_debut"] = mysql_result($req_heure, $b, "heuredebut_definie_periode");
		$tab_select_heure[$b]["heure_fin"] = mysql_result($req_heure, $b, "heurefin_definie_periode");

		echo ("<OPTION value='".$tab_select_heure[$b]["id_heure"]."'>".$tab_select_heure[$b]["creneaux"]." : ".$tab_select_heure[$b]["heure_debut"]." - ".$tab_select_heure[$b]["heure_fin"]."</OPTION>\n");

	}
echo "</SELECT>\n";

	// choix du jour

	$req_jour = mysql_query("SELECT id_horaire_etablissement, jour_horaire_etablissement FROM horaires_etablissement");
	$rep_jour = mysql_fetch_array($req_jour);

echo "<SELECT name=\"jour_semaine\">\n";
echo "<OPTION value=\"rien\">Jour</OPTION>\n";
	$tab_select_jour = array();

	for($a=0;$a<=count($rep_jour);$a++) {
		$tab_select_jour[$a]["id"] = mysql_result($req_jour, $a, "id_horaire_etablissement");
		$tab_select_jour[$a]["jour_sem"] = mysql_result($req_jour, $a, "jour_horaire_etablissement");

		echo ("<OPTION value='".$tab_select_jour[$a]["id"]."'>".$tab_select_jour[$a]["jour_sem"]."</OPTION>\n");
	}
echo "</SELECT>\n";
echo "<br />\n<br />\n";

	// choix de la semaine

	$req_semaine = mysql_query("SELECT * FROM edt_semaines");
	$rep_semaine = mysql_fetch_array($req_semaine);

echo "<SELECT name=\"semaine\">\n";
echo "<OPTION value=\"rien\">Semaine</OPTION>\n";
	$tab_select_semaine = array();

	for($d=0;$d<52;$d++) {
		$tab_select_semaine[$d]["id_semaine"] = mysql_result($req_semaine, $d, "id_edt_semaine");
		$tab_select_semaine[$d]["num_semaine"] = mysql_result($req_semaine, $d, "num_edt_semaine");
		$tab_select_semaine[$d]["type_semaine"] = mysql_result($req_semaine, $d, "type_edt_semaine");

		echo ("<OPTION value='".$tab_select_semaine[$d]["id_semaine"]."'>Semaine n° ".$tab_select_semaine[$d]["num_semaine"]." (".$tab_select_semaine[$d]["type_semaine"].") </OPTION>\n");

	}
echo "</SELECT>\n";

	// choix de la salle
echo "<SELECT  name=\"login_salle\">\n";
echo "<OPTION value=\"rien\">Salle</OPTION>\n";
	$tab_select = renvoie_liste("salle");

	for($i=0;$i<count($tab_select);$i++) {

		echo ("<OPTION value='".$tab_select[$i]["id_salle"]."'>".$tab_select[$i]["nom_salle"]."</OPTION>\n");
	}
echo "</SELECT>\n";

echo "<br />\n<br />\n";

echo "<input type=\"submit\" name=\"Valider\" value=\"Valider\" />\n<br />\n<br />\n";
echo "</FORM>\n";


?>

Cette page permet de rentrer une modification temporaire dans l'emploi du temps pour une semaine donn&eacute;e