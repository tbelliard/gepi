<?php
/*
 * $Id$
 *
 * Copyright 2001, 2021 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

@set_time_limit(0);

// Initialisations files
require_once("../lib/initialisationsPropel.inc.php");
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

$sql="SELECT 1=1 FROM droits WHERE id='/cahier_texte_2/documents_cdt.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/cahier_texte_2/documents_cdt.php',
administrateur='F',
professeur='V',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Documents joints aux CDT',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$msg="";

$entete=isset($_POST['entete']) ? $_POST['entete'] : (isset($_GET['entete']) ? $_GET['entete'] : 'y');

$id_groupe=isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL);
$type_ct=isset($_POST['type_ct']) ? $_POST['type_ct'] : (isset($_GET['type_ct']) ? $_GET['type_ct'] : "");

// A FAIRE : Pouvoir modifier
$ordre="ASC";

$javascript_specifique[] = "lib/tablekit";
$utilisation_tablekit="ok";
//**************** EN-TETE *****************
if($entete=='y') {
	$titre_page = "Documents joints aux CDT";
	require_once("../lib/header.inc.php");
}
//**************** FIN EN-TETE *****************


if(isset($id_groupe)) {

	if($entete=='y') {
		echo "<h2>Documents joints aux cahiers de textes</h2>";
	}
	// Tester les valeurs pour id_groupe

	// Gérer le cas où id_groupe est 'TOUS' ou un tableau

	// Intercaler les documents joints à des travaux à faire
	// type_ct

	if(is_array($id_groupe)) {
		echo "<p style='color:red'>Sélection de plusieurs enseignements simultanément non encore implémentée.</p>";

/*
			echo "<table class='boireaus boireaus_alt sortable resizable'>
	<thead>
		<tr>
			<th class='text'>Enseignement</th>
			".(($type_ct=='') ? "<th class='text' title=\"Compte-rendu ou travail à faire\">Type</th>" : "")."
			<th class='nosort'>Insérer</th>
			<th class='text'>Fichier</th>
			<th class='number'>Taille</th>
			<th class='number'>Date</th>
		</tr>
	</thead>
	<tbody>";
			while($lig=mysqli_fetch_object($res)) {
				echo "
		<tr>
			<td>".get_info_grp($id_groupe, array('classes'))."</td>";
				if($type_ct=='') {
					if(preg_match('|^../documents/cl_dev|', $lig->emplacement)) {
						echo "
			<td>
				<img src='../images/icons/notices_CDT_travail.png' class='icone16' title='Travail à faire' />
			</td>";
					}
					else {
						echo "
			<td>
				<img src='../images/icons/notices_CDT_compte_rendu.png' class='icone16' title='Compte rendu de séance' />
			</td>";
					}
				}
				echo "

			<td><a href=\"javascript:insere_texte_dans_ckeditor('<a href=\'".$lig->emplacement."\' target=\'_blank\'>".preg_replace("/'/", ' ', $lig->titre)."</a>')\" title=\"Insérer un lien vers le document.\"><img src='../images/icons/wizard.png' class='icone16' /></a></td>

			<td style='text-align:left'>
				<a href='".$lig->emplacement."' target='_blank'>".$lig->titre."</a>
			</td>
			<td><span style='display:none'>".$lig->taille."</span>".volume_human($lig->taille)."</td>
			<td><span style='display:none'>".$lig->date_ct."</span>".strftime('%a %d/%m/%Y', $lig->date_ct)."</td>
		</tr>";
			}
			echo "
	</tbody>
</table>";
*/

	}
	elseif(!preg_match('/^[0-9]{1,}$/', $id_groupe)) {
		echo "<p style='color:red'>Identifiant d'enseignement non valide.</p>";
	}
	elseif(!is_prof_groupe($_SESSION['login'], $id_groupe)) {
		echo "<p style='color:red'>Vous n'êtes pas enseignant dans le groupe choisi.</p>";
	}
	else {
		//echo "<p>".get_info_grp($id_groupe, array('classes'))."</p>";
		echo "<p class='bold'>".get_info_grp($id_groupe);
		if($type_ct=='c') {
			echo " <img src='../images/icons/notices_CDT_compte_rendu.png' class='icone16' title='Compte-rendus de séance' />";
		}
		elseif($type_ct=='t') {
			echo " <img src='../images/icons/notices_CDT_travail.png' class='icone16' title='Travaux à faire' />";
		}
		else {
			echo " <img src='../images/icons/notices_CDT_compte_rendu.png' class='icone16' title='Compte-rendus de séance' />";
			echo " <img src='../images/icons/notices_CDT_travail.png' class='icone16' title='Travaux à faire' />";
		}
		echo "</p>";

		if($type_ct=='c') {
			$sql="SELECT cd.*,ce.date_ct FROM ct_documents cd, 
					ct_entry ce 
				WHERE ce.id_groupe='".$id_groupe."' AND 
					ce.id_ct=cd.id_ct 
				ORDER BY ce.date_ct $ordre";
		}
		elseif($type_ct=='t') {
			$sql="SELECT cd.*,ce.date_ct FROM ct_devoirs_documents cd, 
					ct_devoirs_entry ce 
				WHERE ce.id_groupe='".$id_groupe."' AND 
					ce.id_ct=cd.id_ct_devoir 
				ORDER BY ce.date_ct $ordre";
		}
		else {

			$sql="(SELECT cd.*,ce.date_ct FROM ct_documents cd, 
					ct_entry ce 
				WHERE ce.id_groupe='".$id_groupe."' AND 
					ce.id_ct=cd.id_ct 
				ORDER BY ce.date_ct $ordre) 
				UNION 
				(SELECT cd.*,ce.date_ct FROM ct_devoirs_documents cd, 
					ct_devoirs_entry ce 
				WHERE ce.id_groupe='".$id_groupe."' AND 
					ce.id_ct=cd.id_ct_devoir 
				ORDER BY ce.date_ct $ordre)";
		}
		//echo "$sql<br />";
		$res=mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($res)==0) {
			echo "<p>Aucun document n'est joint aux compte-rendus de séances.</p>";
		}
		else {
			echo "<table class='boireaus boireaus_alt sortable resizable'>
	<thead>
		<tr>
			".(($type_ct=='') ? "<th class='text' title=\"Compte-rendu ou travail à faire\">Type</th>" : "")."
			".(($entete=='n') ? "<th class='nosort'>Insérer</th>" : "")."
			<th class='text' style='max-width:600px; overflow:auto;'>Fichier</th>
			<th class='number'>Taille</th>
			<th class='number'>Date</th>
		</tr>
	</thead>
	<tbody>";
			while($lig=mysqli_fetch_object($res)) {
				echo "
		<tr>";
				if($type_ct=='') {
					if(preg_match('|^../documents/cl_dev|', $lig->emplacement)) {
						echo "
			<td>
				<img src='../images/icons/notices_CDT_travail.png' class='icone16' title='Travail à faire' />
			</td>";
					}
					else {
						echo "
			<td>
				<img src='../images/icons/notices_CDT_compte_rendu.png' class='icone16' title='Compte rendu de séance' />
			</td>";
					}
				}
				echo "
			".(($entete=='n') ? "<td><a href=\"javascript:insere_texte_dans_ckeditor('<a href=\'".$lig->emplacement."\' target=\'_blank\'>".preg_replace("/'/", ' ', $lig->titre)."</a>')\" title=\"Insérer un lien vers le document.\"><img src='../images/icons/wizard.png' class='icone16' /></a></td>" : "")."

			<td style='text-align:left;max-width:600px; overflow:hidden;'>
				<a href='".$lig->emplacement."' target='_blank' title=\"".$lig->titre."\">".$lig->titre."</a>
			</td>
			<td><span style='display:none'>".$lig->taille."</span>".volume_human($lig->taille)."</td>
			<td><span style='display:none'>".$lig->date_ct."</span>".strftime('%a %d/%m/%Y', $lig->date_ct)."</td>
		</tr>";
			}
			echo "
	</tbody>
</table>";
		}
	}
}
else {
	echo "<h2>Documents joints aux cahiers de textes</h2>
	<p style='color:red; margin-bottom:1em;'>L'enseignement n'est pas encore choisi.</p>";

	if($entete=='y') {
		// Choisir un groupe:
		// Choisir Compte-rendus et/ou devoirs à faire

		$tab=get_groups_for_prof($_SESSION['login']);

		if(count($tab)==0) {
			echo "<p style='color:red'>Vous ne semblez être enseignant dans aucun groupe.</p>";
		}
		else {
			echo "<p>Pour quel enseignement voulez-vous afficher les documents joints aux CDT&nbsp;?</p>
			<ul>";
			foreach($tab as $key => $current_group) {
				echo "
				<li>
					<a href='".$_SERVER['PHP_SELF']."?id_groupe=".$current_group['id']."'>".$current_group['name']." (".$current_group['classlist_string'].")</a> - 
					<a href='".$_SERVER['PHP_SELF']."?id_groupe=".$current_group['id']."&type_ct=c'>comptes-rendus seuls</a> - 
					<a href='".$_SERVER['PHP_SELF']."?id_groupe=".$current_group['id']."'&type_ct=t>travaux à faire seuls</a>
				</li>";
			}
			echo "
			</ul>";
		}
	}

}

?>
