<?php

/**
 *
 * @version $Id$
 *
 * Fichier destiné à gérer les accès responsables et élèves du module absences
 *
 * Copyright 2001, 2008 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// ================================ Initiatisation de base ======================
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

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

//On vérifie si le module est activé
if (mb_substr(getSettingValue("active_module_absence"),0,1)!='y') {
	header("Location: ../accueil.php");
    die("Le module n'est pas activé.");
}
elseif (mb_substr(getSettingValue("active_absences_parents"),0,1)!='y'){
	// On vérifie aussi que l'accès parents est bien autorisé
	header("Location: ../accueil.php");
	die("Le module n'est pas activé.");
}

// =============================== fin initialisation de base ===================
// =============================== Ensemble des opérations php ==================

// on met le header ici pour récupérer des infos sur les enfants
$style_specifique = 'mod_absences/styles/parents_absences';
$javascript_specifique = '';
//**************** EN-TETE *****************
$titre_page = "Les absences";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

// On récupère du header les infos sur les enfants : $tab_tmp_ele
// Sécurité supplémentaire car il faut nécessairement être un responsable pour avoir ces infos
$aff_absences = array();
$nbre = count($tab_tmp_ele);

for($i = 0; $i < $nbre; ){
	$aff_absences[$i] = '';
	$n = $i + 1; // pour le nom et le prénom de l'élève

	// On récupère toutes les absences qui correspondent à ce login
	$query = mysql_query("SELECT * FROM absences_eleves WHERE eleve_absence_eleve = '".$tab_tmp_ele[$i]."' ORDER BY a_date_absence_eleve")
					OR DIE('Erreur dans la récupération des absences de votre enfant : '.mysql_error());
	$nbre_absence = mysql_num_rows($query);

	// et on les mets en forme
	for($a = 0; $a < $nbre_absence; $a++){
		// on récupère ce dont on a besoin
		$abs[$a]["d_date_absence_eleve"] = mysql_result($query, $a, "d_date_absence_eleve");
		$abs[$a]["a_date_absence_eleve"] = mysql_result($query, $a, "a_date_absence_eleve");
		$abs[$a]["heuredeb_absence"] = mysql_result($query, $a, "d_heure_absence_eleve");
		$abs[$a]["heurefin_absence"] = mysql_result($query, $a, "a_heure_absence_eleve");
		$abs[$a]["justification"] = mysql_result($query, $a, "justify_absence_eleve");
		$abs[$a]["type"] = mysql_result($query, $a, "type_absence_eleve");
		$abs[$a]["id"] = mysql_result($query, $a, "id_absence_eleve");
		// on vérifie le type
		if ($abs[$a]["type"] == "A") {
			$type = "<td style=\"abs\">Abs.</td>";
		}elseif($abs[$a]["type"] == "R"){
			$type = "<td style=\"ret\">Ret.</td>";
		}else {
			$type = "<td>-</td>";

		}
		// on vérifie la justification
		if ($abs[$a]["justification"] == "N") {
			$justifie = "<td>Non justifiée</td>";
		}elseif($abs[$a]["justification"] == "T"){
			$justifie = "<td>Par tel.</td>";
		}elseif($abs[$a]["justification"] == "O"){
			$justifie = "<td>Oui</td>";
		}else{
			$justifie = "<td> - </td>";
		}
		// on construit la ligne
		$aff_absences[$a] = '
			<tr>
				<td>'.$tab_tmp_ele[$n].'</td>'
				.$type.'
				<td>Du '.$abs[$a]["d_date_absence_eleve"].' à '.$abs[$a]["heuredeb_absence"].'</td>
				<td>Au '.$abs[$a]["a_date_absence_eleve"].' à '.$abs[$a]["heurefin_absence"].'</td>'
				.$justifie.'
				<td>non</td>
			</tr>
		';

	}
	// On vérifie si les bulletins ont été renseignés pour les différentes périodes
	$query_b = mysql_query("SELECT * FROM absences WHERE login = '".$tab_tmp_ele[$i]."' ORDER BY periode");
	$verif = mysql_num_rows($query_b);
		$aff_absences_bulletin = '';
	if ($verif >= 1) {
		$aff_absences_bulletin .= '<br /><br />
		<table id="absBull">
			<caption title="Ces absences sont enregistrées sur le bulletin après traitement et vérification.">
			Les absences retenues sur le bulletin</caption>
			<thead>
				<tr>
					<th>Elève concerné</th>
					<th>Période</th>
					<th>Nbre d\'absences</th>
					<th>dont non justifiées</th>
					<th>Nbre de retards</th>
					<th>Appréciation</th>
				</tr>
			</thead>
			<tbody>
		';
		for($ab = 0; $ab < $verif; $ab++){
			$absbull[$ab]["periode"] = mysql_result($query_b, $ab, "periode");
			$absbull[$ab]["nb_absences"] = mysql_result($query_b, $ab, "nb_absences");
			$absbull[$ab]["non_justifie"] = mysql_result($query_b, $ab, "non_justifie");
			$absbull[$ab]["nb_retards"] = mysql_result($query_b, $ab, "nb_retards");
			$absbull[$ab]["appreciation"] = mysql_result($query_b, $ab, "appreciation");
			if ($absbull[$ab]["appreciation"] == "") {
				$appreciation = "Aucune";
			}else {
				$appreciation = $absbull[$ab]["appreciation"];
			}
			// On construit le tableau
			$aff_absences_bulletin .= '
				<tr>
				<td>'.$tab_tmp_ele[$n].'</td>
				<td>'.$absbull[$ab]["periode"].'</td>
				<td>'.$absbull[$ab]["nb_absences"].'</td>
				<td>'.$absbull[$ab]["non_justifie"].'</td>
				<td>'.$absbull[$ab]["nb_retards"].'</td>
				<td>'.$appreciation.'</td>
				</tr>
			';
		}
		$aff_absences_bulletin .= '</tbody></table>'."\n";
	} // if ($verif >= 1)...


	$i = $i + 2;
// MODIF } // fin for($i = 0; $i < count($tab_tmp_ele); ...

// =============================== Fin des opérations php =======================

?>
<!-- Debut de la page absences parents -->
<h2>Les Absences de <?php echo $tab_tmp_ele[$n]; ?></h2>

<table id="abs">
	<caption title="Ces absences sont l'ensemble des saisies enregistr&eacute;es avant v&eacute;rification">Les absences enregistr&eacute;es dans l'&eacute;tablissement</caption>
	<thead>
		<tr>
			<th>El&egrave;ve concern&eacute;</th>
			<th>Abs. / Ret.</th>
			<th>Date et heure de d&eacute;but de l'absence</th>
			<th>Date et heure de fin de l'absence</th>
			<th>Justification</th>
			<th>Proposer un justificatif d'absence</th>
		</tr>
	</thead>
	<tbody>

<?php // on affiche toutes les lignes
for($c = 0; $c < $nbre_absence; $c++){
	echo $aff_absences[$c]."\n";
}
?>
	</tbody>

</table>

<?php // Si les bulletins sont renseignés, on affiche les infos relatives aux absences
  if (isset($aff_absences_bulletin) AND $aff_absences_bulletin != "") {
    echo $aff_absences_bulletin;
  }
} // fin for($i = 0; $i < count($tab_tmp_ele)
echo "<!-- fin de la page absences parents -->";
// on inclut le footer
require("../lib/footer.inc.php");
?>