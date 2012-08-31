<?php

/*
 *
 * Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

$variables_non_protegees = 'yes';

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

// SQL : INSERT INTO droits VALUES ( '/classes/classes_ajax_lib.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Ajax', '');
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/classes/classes_ajax_lib.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Ajax', '');";

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
	die();
}


$mode=isset($_GET['mode']) ? $_GET['mode'] : NULL;

if($mode=='classes_param') {
	//$cpt=isset($_GET['cpt']) ? $_GET['cpt'] : NULL;
	$matiere=isset($_GET['matiere']) ? $_GET['matiere'] : NULL;
	$matiere=my_ereg_replace("[^A-Za-z0-9_.-]","",$matiere);

	if(isset($matiere)) {
		if($matiere=="") {
			echo "Pas de mati&egrave;re s&eacute;lectionn&eacute;e.";
		}
		else {
			$sql="SELECT u.login,u.nom,u.prenom FROM utilisateurs u, j_professeurs_matieres jpm WHERE jpm.id_professeur=u.login AND id_matiere='$matiere' AND etat='actif' ORDER BY u.nom,u.prenom;";
			//echo "$sql<br />";
			$res_prof=mysql_query($sql);

			echo "<select name='professeur_nouvel_enseignement'>\n";
			//echo "<option value=''>---</option>\n";
			if(mysql_num_rows($res_prof)>0) {
				while($lig_prof=mysql_fetch_object($res_prof)) {
					echo "<option value='$lig_prof->login'>".my_strtoupper($lig_prof->nom)." ".casse_mot($lig_prof->prenom,'majf2')."</option>\n";
				}
			}
			echo "</select>\n";
		}
	}
	else {
		echo "La variable \$matiere n'est pas d&eacute;finie.";
	}
}

if($mode=='ouvrir_infobulle_nav') {
	$ouvrir_infobulle_nav=getSettingValue("ouvrir_infobulle_nav");
	if($ouvrir_infobulle_nav=='y') {
		saveSetting("ouvrir_infobulle_nav", 'n');
		echo "<a href='#' onclick='modif_mode_infobulle_nav();return false;'><img src='../images/rouge.png' width='16' height='16' /></a>\n";
	}
	else {
		saveSetting("ouvrir_infobulle_nav", 'y');
		echo "<a href='#' onclick='modif_mode_infobulle_nav();return false;'><img src='../images/vert.png' width='16' height='16' /></a>\n";
	}
}

?>
