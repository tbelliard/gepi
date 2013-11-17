<?php

/**
 *
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

$titre_page = "Cahier de textes du jour";

$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");
include("../edt_organisation/fonctions_calendrier.php");

// fonctions complémentaires et/ou librairies utiles


// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == "c") {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == "0") {
    header("Location: ../logout.php?auto=1");
    die();
}

// Sécurité
// SQL : INSERT INTO droits VALUES ( './cahier_texte/visu_prof_jour.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Acces_a_son_cahier_de_textes_personnel', '');
// maj : $tab_req[] = "INSERT INTO droits VALUES ( './cahier_texte/visu_prof_jour.php', 'F', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Acces_a_son_cahier_de_textes_personnel', '');";
//
if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}

// ======================== Initialisation des données ==================== //





// ======================== Traitement des données ======================== //
$today_ts = mktime(23, 59, 00, date("m"), date("d"), date("Y"));
$une_semaine = 604800; // en secondes
$semaine_prec = $une_semaine - 86400; // permet de construire la requête qui cherche les notices à afficher
$ts_semaine_avant = $today_ts - $une_semaine;

$today_jour = retourneJour(""); // retourne le jour de la semaine en toutes lettres et en Français

$sql = "SELECT * FROM ct_entry WHERE id_login = '".$_SESSION['login']."'
								AND date_ct
								BETWEEN '".($ts_semaine_avant - 86400)."' AND '".$ts_semaine_avant."'
								ORDER BY heure_entry";

$query = mysqli_query($GLOBALS["___mysqli_ston"], $sql) OR DIE('ERREUR SQL : ' . $sql . '<br />&nbsp;--> ' . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

$a = 0;
while($rep = mysqli_fetch_array($query)){

	$tab_rep[$a]['heure'] = $rep['heure_entry'];
	$tab_rep[$a]['groupe'] = $rep['id_groupe'];
	$tab_rep[$a]['date'] = date("d-m-Y", $rep['date_ct']);
	$tab_rep[$a]['contenu'] = $rep['contenu'];
	$a++;

}
if ($a === 0) {
	$tab_rep[$a]['heure'] = "";
	$tab_rep[$a]['groupe'] = "";
	$tab_rep[$a]['date'] = "";
	$tab_rep[$a]['contenu'] = "";
}

// ======================== CSS et js particuliers ========================
$utilisation_win = "oui";
$utilisation_jsdivdrag = "non";
//$javascript_specifique = "";
//$style_specifique = "";

// ===================== entete Gepi ======================================//
require_once("../lib/header.inc.php");
// ===================== fin entete =======================================//

echo "<!-- page Cahier_de_textes_du_jour.-->";

?>

<table>
	<tr>
		<th>heure</th>
		<th>date</th>
		<th>groupe</th>
		<th>Contenu</th>
	</tr>

	<?php foreach($tab_rep as $aff): ?>

	<tr style="border: 1px solid grey; background: silver;">
		<td><?php echo $aff["heure"]; ?></td>
		<td><?php echo $aff["date"]; ?></td>
		<td><?php echo $aff["groupe"]; ?></td>
		<td><?php echo $aff["contenu"]; ?></td>
	</tr>

	<?php endforeach; ?>
</table>



<?php
// Inclusion du bas de page
require_once("../lib/footer.inc.php");
?>
