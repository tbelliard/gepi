<?php
/**
 *
 * @version $Id: chercher_edt.php 4152 2010-03-21 23:32:16Z adminpaulbert $
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
// Fichier qui permet de faire des recherches dans l'EdT

$auto_aff_1 == 0;
$auto_aff_2 == 0;
$auto_aff_21 == 0;
$auto_aff_22 == 0;

if ($_GET["salleslibres"] == "ok") {
	$auto_aff_1 = 1;
}


if ($_GET["cherch_salle"] == "ok") {
	if (isset($_GET["ch_heure"])) {
		$auto_aff_21 = 1;
	}
	else
		echo ("Vous devez choisir un créneau, revenez en arrière !");

	if (isset($_GET["ch_jour_semaine"])) {
		$auto_aff_22 = 1;
	}
	else
		echo ("Vous devez choisir un jour de la semaine, revenez en arrière !");

	if (($auto_aff_21 == 1) AND ($auto_aff_22 == 1)) {
		$auto_aff_2 = 1;
	}
}

?>

Cette page sert &agrave; trouver les salles de cours occup&eacute;es et libres &agrave; un horaire de la semaine.

Pour cela, veuillez choisir un cr&eacute;neau et un jour de la semaine :

<?php

if ($auto_aff_1 === 1) {

echo '
<form action="index_edt.php" name="chercher" method="get">
	<input type="hidden" name="salleslibres" value="ok" />
	<input type="hidden" name="cherch_salle" value="ok" />
	';

	// choix de l'horaire

	$req_heure = mysql_query("SELECT id_definie_periode, nom_definie_periode, heuredebut_definie_periode, heurefin_definie_periode FROM edt_creneaux ORDER BY heuredebut_definie_periode");
	$rep_heure = mysql_fetch_array($req_heure);

echo '
	<select name="ch_heure">
		<option value=\'rien\'>Horaire</option>
	';
	$tab_select_heure = array();

	for($b=0;$b<count($rep_heure);$b++) {
		$tab_select_heure[$b]["id_heure"] = mysql_result($req_heure, $b, "id_definie_periode");
		$tab_select_heure[$b]["creneaux"] = mysql_result($req_heure, $b, "nom_definie_periode");
		$tab_select_heure[$b]["heure_debut"] = mysql_result($req_heure, $b, "heuredebut_definie_periode");
		$tab_select_heure[$b]["heure_fin"] = mysql_result($req_heure, $b, "heurefin_definie_periode");

		echo '
		<option value=\''.$tab_select_heure[$b]["id_heure"].'\'>'.$tab_select_heure[$b]["creneaux"].' : '.$tab_select_heure[$b]["heure_debut"].' - '.$tab_select_heure[$b]["heure_fin"].'</option>
			';

	}
echo '
	</select>
	<br />
	<br />
	';

	// choix du jour

	$req_jour = mysql_query("SELECT id_horaire_etablissement, nom_horaire_etablissement FROM horaires_etablissement");
	$rep_jour = mysql_fetch_array($req_jour);

echo '
	<select name="ch_jour_semaine">
		<option value="rien">Jour</option>';
	$tab_select_jour = array();

	for($a=0; $a<count($rep_jour); $a++) {
		$tab_select_jour[$a]["id"] = mysql_result($req_jour, $a, "id_horaire_etablissement");
		$tab_select_jour[$a]["jour_sem"] = mysql_result($req_jour, $a, "jour_horaire_etablissement");

		echo '
		<option value=\''.$tab_select_jour[$a]["jour_sem"].'\'>'.$tab_select_jour[$a]["jour_sem"].'</option>
		';
	}
echo '
	</select>
	<br />
	<br />
	';

	/* choix de la semaine

	$req_semaine = mysql_query("SELECT * FROM edt_semaines");
	$rep_semaine = mysql_fetch_array($req_semaine);

echo '<SELECT name="semaine">';
echo ('<OPTION value=\'rien\'>Semaine</OPTION>');
	$tab_select_semaine = array();

	for($d=0;$d<52;$d++) {
		$tab_select_semaine[$d]["id_semaine"] = mysql_result($req_semaine, $d, "id_edt_semaine");
		$tab_select_semaine[$d]["num_semaine"] = mysql_result($req_semaine, $d, "num_edt_semaine");
		$tab_select_semaine[$d]["type_semaine"] = mysql_result($req_semaine, $d, "type_edt_semaine");

		echo ('<OPTION value=\''.$tab_select_semaine[$d]["id_semaine"].'\'>Semaine n° '.$tab_select_semaine[$d]["num_semaine"].' ('.$tab_select_semaine[$d]["type_semaine"].') </OPTION>');

	}
echo '</SELECT>';*/
echo '
		<input type="submit" name="Valider" value="Valider" />
		<br />
		<br />
	</form>
	';

}

if ($auto_aff_2 === 1) {
	$salles_libres = aff_salles_vides($_GET["ch_heure"], $_GET["ch_jour_semaine"]);
		for($s=0; $s<count($salles_libres); $s++)

		echo "$salles_libres[$s].'<br />'";
}

?>