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

//$variables_non_protegees = 'yes';

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

if(mb_strtolower(mb_substr(getSettingValue('active_mod_discipline'),0,1))!='y') {
	$mess=rawurlencode("Vous tentez d accéder au module Discipline qui est désactivé !");
	tentative_intrusion(1, "Tentative d'accès au module Discipline qui est désactivé.");
	header("Location: ../accueil.php?msg=$mess");
	die();
}

// Page appelée via ajax depuis saisie_sanction.php->liste_retenues_jour.php

$date=$_GET['date'];

if(!isset($date)) {
	echo "<p><strong>Erreur&nbsp;:</strong> Des paramètres n'ont pas été transmis.</p>\n";
}
else {

	require('sanctions_func_lib.php');

	$tab_date=explode("/",$date);
	$annee=$tab_date[2];
	$mois=$tab_date[1];
	$jour=$tab_date[0];

	/*
	if(!checkdate($tmp_date[1],$tmp_date[0],$tmp_date[2])) {
		$msg.="La date saisie n'est pas valide.<br />";
	}
	*/

	if((!preg_match("/^[0-9]*$/",$annee))||
	(!preg_match("/^[0-9]*$/",$mois))||
	(!preg_match("/^[0-9]*$/",$jour))) {
		echo "<p style='color:red;'>La date '$date' est invalide.</p>\n";
	}
	else {
		$sql="SELECT * FROM s_sanctions s, s_retenues sr WHERE s.id_sanction=sr.id_sanction AND date='".$annee."-".$mois."-".$jour."' ORDER BY heure_debut, lieu;";
		$res_sanction=mysql_query($sql);
		if(mysql_num_rows($res_sanction)>0) {
	
			echo "<table class='boireaus' summary='Retenues du jour'>\n";
			echo "<tr>\n";
			echo "<th style='font-size:x-small;'>Nature</th>\n";
			echo "<th style='font-size:x-small;'>Heure</th>\n";
			echo "<th style='font-size:x-small;'>Dur&eacute;e</th>\n";
			echo "<th style='font-size:x-small;'>Lieu</th>\n";
			echo "<th style='font-size:x-small;'>El&egrave;ve</th>\n";
			echo "</tr>\n";
	
			$alt=1;
			while($lig_sanction=mysql_fetch_object($res_sanction)) {
	
				$date=formate_date($lig_sanction->date);
				$heure_debut=$lig_sanction->heure_debut;
				$duree=$lig_sanction->duree;
				$lieu=$lig_sanction->lieu;
				$travail=$lig_sanction->travail;
				$current_eleve_login=$lig_sanction->login;
	
				$alt=$alt*(-1);
				echo "<tr class='lig$alt'>\n";
				echo "<td style='font-size:x-small;'>".ucfirst($lig_sanction->nature)."</td>\n";
				echo "<td style='font-size:x-small;'>$heure_debut</td>\n";
				echo "<td style='font-size:x-small;'>$duree</td>\n";
				echo "<td style='font-size:x-small;'>$lieu</td>\n";
				echo "<td style='font-size:x-small;'>";
				echo htmlspecialchars(p_nom($current_eleve_login));
	
				echo " (<em>";
				$tmp_tab=get_class_from_ele_login($current_eleve_login);
				//if(isset($tmp_tab['liste_nbsp'])) {echo htmlspecialchars($tmp_tab['liste_nbsp']);}
				if(isset($tmp_tab['liste'])) {echo preg_replace("/ /","&nbsp;",htmlspecialchars($tmp_tab['liste']));}
				echo "</em>)";
	
				echo "</td>\n";
				echo "</tr>\n";
			}
			echo "</table>\n";
		}
		else {
			echo "<p>Aucune retenue n'est encore saisie<br />pour ce jour (<em>$date</em>).</p>\n";
		}
	}
}
?>
