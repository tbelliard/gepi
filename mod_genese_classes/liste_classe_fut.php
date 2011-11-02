<?php

/*
 * $Id: liste_classe_fut.php 7357 2011-07-01 16:35:26Z crob $
 *
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

//======================================================================================

$sql="SELECT 1=1 FROM droits WHERE id='/mod_genese_classes/liste_classe_fut.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_genese_classes/liste_classe_fut.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Génèse des classes: Liste des classes futures (appel ajax)',
statut='';";
$insert=mysql_query($sql);
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
	die();
}

// Page appelée via ajax depuis saisie_sanction.php->liste_retenues_jour.php

$classe_fut=isset($_GET['classe_fut']) ? $_GET['classe_fut'] : NULL;
$projet=isset($_GET['projet']) ? $_GET['projet'] : NULL;
$ele_login=isset($_GET['ele_login']) ? $_GET['ele_login'] : NULL;
$avec_classe_origine=isset($_GET['avec_classe_origine']) ? $_GET['avec_classe_origine'] : 'n';

if((!isset($classe_fut))||(!isset($projet))) {
	echo "<p><strong>Erreur&nbsp;:</strong> Des param&egrave;tres n'ont pas &eacute;t&eacute; transmis.</p>\n";
}
else {

	echo "<p style='text-align:center; font-weight:bold;'>";
	if(isset($ele_login)) {
		$sql="SELECT nom, prenom FROM eleves WHERE login='$ele_login';";
		$res_ele_courant=mysql_query($sql);

		if(mysql_num_rows($res_ele_courant)>0) {
			$lig_ele_courant=mysql_fetch_object($res_ele_courant);

			echo htmlentities(strtoupper($lig_ele_courant->nom))." ".htmlentities(ucfirst(strtolower($lig_ele_courant->prenom)));
			if($avec_classe_origine) {
				$tmp_tab_clas=get_class_from_ele_login($ele_login);
				if(isset($tmp_tab_clas['liste'])) {
					echo " <span style='font-size:x-small'>(".$tmp_tab_clas['liste'].")</span>";
				}
			}
			echo " -&gt; ";
		}
	}
	echo htmlentities($classe_fut)."</p>\n";

	//$sql="SELECT e.nom,e.prenom FROM gc_eleve_fut_classe g, eleves e WHERE g.projet='$projet' AND g.classe='$classe_fut' AND g.login=e.login ORDER BY nom, prenom;";
	$sql="SELECT e.login,e.nom,e.prenom FROM gc_eleves_options g, eleves e WHERE g.projet='$projet' AND g.classe_future='$classe_fut' AND g.login=e.login ORDER BY nom, prenom;";
	$res_ele_clas_fut=mysql_query($sql);
	$eff_ele_clas_fut=mysql_num_rows($res_ele_clas_fut);
	if($eff_ele_clas_fut>0) {

		echo "<div align='center'>\n";

		if($eff_ele_clas_fut<=8) {

			echo "<table class='boireaus' summary='El&egrave;ves de $classe_fut'>\n";
			echo "<tr>\n";
			//echo "<th style='font-size:x-small;'>El&egrave;ve</th>\n";
			echo "<th>El&egrave;ve</th>\n";
			echo "</tr>\n";
	
			$alt=1;
			while($lig_ele_clas_fut=mysql_fetch_object($res_ele_clas_fut)) {
	
				$alt=$alt*(-1);
				echo "<tr class='lig$alt'>\n";
				//echo "<td style='font-size:x-small;'>".htmlentities(strtoupper($lig_ele_clas_fut->nom))." ".htmlentities(ucfirst(strtolower($lig_ele_clas_fut->prenom)))."</td>\n";
				echo "<td>".htmlentities(strtoupper($lig_ele_clas_fut->nom))." ".htmlentities(ucfirst(strtolower($lig_ele_clas_fut->prenom)));
				if($avec_classe_origine) {
					$tmp_tab_clas=get_class_from_ele_login($lig_ele_clas_fut->login);
					if(isset($tmp_tab_clas['liste'])) {
						echo " <span style='font-size:x-small'>(".$tmp_tab_clas['liste'].")</span>";
					}
				}
				echo "</td>\n";
				echo "</tr>\n";
			}
			echo "</table>\n";

		}
		else {

			echo "<table summary='Separation'>\n";
			echo "<tr>\n";
			echo "<td>\n";

				echo "<table class='boireaus' summary='El&egrave;ves de $classe_fut'>\n";
				echo "<tr>\n";
				//echo "<th style='font-size:x-small;'>El&egrave;ve</th>\n";
				echo "<th>El&egrave;ve</th>\n";
				echo "</tr>\n";
	
			$cpt=0;
			$alt=1;
			while($lig_ele_clas_fut=mysql_fetch_object($res_ele_clas_fut)) {
				if($cpt>=ceil($eff_ele_clas_fut/2)) {
					echo "</table>\n";
					echo "</td>\n";
	
					echo "<td>\n";
					echo "&nbsp;";
					echo "</td>\n";

					$cpt=0;
	
					echo "<td>\n";
						echo "<table class='boireaus' summary='El&egrave;ves de $classe_fut'>\n";
						echo "<tr>\n";
						//echo "<th style='font-size:x-small;'>El&egrave;ve</th>\n";
						echo "<th>El&egrave;ve</th>\n";
						echo "</tr>\n";
						echo "<tr>\n";
				}
	
				$alt=$alt*(-1);
				echo "<tr class='lig$alt'>\n";
				//echo "<td style='font-size:x-small;'>".htmlentities(strtoupper($lig_ele_clas_fut->nom))." ".htmlentities(ucfirst(strtolower($lig_ele_clas_fut->prenom)))."</td>\n";
				echo "<td>".htmlentities(strtoupper($lig_ele_clas_fut->nom))." ".htmlentities(ucfirst(strtolower($lig_ele_clas_fut->prenom)));
				if($avec_classe_origine) {
					$tmp_tab_clas=get_class_from_ele_login($lig_ele_clas_fut->login);
					if(isset($tmp_tab_clas['liste'])) {
						echo " <span style='font-size:x-small'>(".$tmp_tab_clas['liste'].")</span>";
					}
				}
				echo "</td>\n";
				echo "</tr>\n";

				$cpt++;
			}
				echo "</table>\n";
			echo "</td>\n";
			echo "</tr>\n";
			echo "</table>\n";

		}

		echo "</div>\n";

	}
	else {
		echo "<p>Aucun &eacute;l&egrave;ve en <b>$classe_fut</b>.</p>\n";
	}
}
?>