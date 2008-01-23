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
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
};

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

//On vérifie si le module est activé
if (getSettingValue("active_module_absence")!='y') {
	header("Location: ../accueil.php");
    die("Le module n'est pas activé.");
}elseif (getSettingValue("active_absences_parents") != 'y'){
		// On vérifie aussi que l'accès parents est bien autorisé
		header("Location: ../accueil.php");
    	die("Le module n'est pas activé.");
}

// =============================== fin initialisation de base ===================
// =============================== Ensemble des opérations php ==================
		/*CREATE TABLE `absences_eleves` (
		  `id_absence_eleve` int(11) NOT NULL auto_increment,
		  `type_absence_eleve` char(1) NOT NULL default '',
		  `eleve_absence_eleve` varchar(25) NOT NULL default '0',
		  `justify_absence_eleve` char(3) NOT NULL default '',
		  `info_justify_absence_eleve` text NOT NULL,
		  `motif_absence_eleve` varchar(4) NOT NULL default '',
		  `info_absence_eleve` text NOT NULL,
		  `d_date_absence_eleve` date NOT NULL default '0000-00-00',
		  `a_date_absence_eleve` date default NULL,
		  `d_heure_absence_eleve` time default NULL,
		  `a_heure_absence_eleve` time default NULL,
		  `saisie_absence_eleve` varchar(50) NOT NULL default '',
		  `heure_retard_eleve` time NOT NULL,
		  PRIMARY KEY  (`id_absence_eleve`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;*/

// on met le header ici pour récupérer des infos sur les enfants
$style_specifique = '';
$javascript_specifique = '';
//**************** EN-TETE *****************
$titre_page = "Les absences";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

// On récupère du header les infos sur les enfants : $tab_tmp_ele
//$tab_tmp_ele=get_enfants_from_resp_login($_SESSION['login']);
$aff_absences = array();
for($i = 0; $i < count($tab_tmp_ele); ){
	$aff_absences[$i] = '';
	$n = $i + 1; // pour le nom de l'élève
	$p = $i + 2; // pour le prénom de l'élève
	// On récupère toutes les absences qui correspondent à ce login
	$query = mysql_query("SELECT * FROM absences_eleves WHERE eleve_absence_eleve = '".$tab_tmp_ele[$i]."' ORDER BY a_date_absence_eleve")
					OR DIE('Erreur dans la récupération des absences de votre enfant : '.mysql_error());
	$nbre_absence = mysql_num_rows($query);
//$debug = mysql_fetch_array($query);
//echo $nbre_absence.' | '.$debug["d_date_absence_eleve"];
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
		$aff_absences[$i] = '
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

	$i = $i + 2;
}
// on détermine quels sont les enfants sous la responsabilité de ce responsable

// =============================== Fin des opérations php =======================

?>
<!-- Debut de la page absences parents -->
<h2>Les absences enregistr&eacute;es dans l'&eacute;tablissement</h2>
<table>
	<thead>
		<tr style="background: silver;">
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

<!-- fin de la page absences parents -->
<?PHP
require("../lib/footer.inc.php");
?>