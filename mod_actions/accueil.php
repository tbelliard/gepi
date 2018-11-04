<?php
/*
 * Copyright 2001, 2018 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Régis Bouguin, Stephane Boireau
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

$sql="SELECT 1=1 FROM droits WHERE id='/mod_actions/accueil.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_actions/accueil.php',
administrateur='F',
professeur='F',
cpe='F',
scolarite='F',
eleve='V',
responsable='V',
secours='F',
autre='F',
description='Actions : Consultation parent/élève',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$terme_mod_action=getSettingValue('terme_mod_action');
$terme_mod_action_nettoye=str_replace("'", " ", str_replace('"', " ", $terme_mod_action));

//**************** FIN EN-TETE *****************
$titre_page = $terme_mod_action."s";
require_once("../lib/header.inc.php");
// debug_var();
//**************** FIN EN-TETE *****************

$retour='../accueil.php';
echo "
<p class='bold'>
	<a href='".$retour."'>
		<img src='../images/icons/back.png' alt='Retour' class='back_link'/>
		Retour
	</a>
</p>

<h2>".$terme_mod_action."s</h2>";

echo tableau_actions_eleve();
/*
if($_SESSION['statut']=='eleve') {
	$sql="SELECT maa.*, 
		mai.presence, 
		mai.date_pointage, 
		mai.login_pointage 
	FROM mod_actions_action maa, 
		mod_actions_inscriptions mai 
	WHERE mai.id_action=maa.id AND 
		mai.login_ele='".$_SESSION['login']."' 
	ORDER BY date_action DESC;";
	//echo "$sql<br />";
	$res=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p style='color:red'>Vous n'êtes inscrit(e) dans aucun(e) ".$terme_mod_action.".</p>";
		require("../lib/footer.inc.php");
		die();
	}
}
else {
	$sql="SELECT maa.*, 
		mai.presence, 
		mai.date_pointage, 
		mai.login_pointage 
	FROM mod_actions_action maa, 
		mod_actions_inscriptions mai 
	WHERE mai.id_action=maa.id AND 
		mai.login_ele IN (SELECT e.login FROM eleves e, 
								responsables2 r, 
								resp_pers rp 
							WHERE e.ele_id=r.ele_id AND 
								r.pers_id=rp.pers_id AND 
								rp.login='".$_SESSION['login']."')
	ORDER BY date_action DESC;";
	//echo "$sql<br />";
	$res=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p style='color:red'>Aucun des élèves/enfants qui vous sont associés n'est inscrit(e) dans un(e) ".$terme_mod_action.".</p>";
		require("../lib/footer.inc.php");
		die();
	}
}

$tab_actions_categories=get_tab_actions_categories();

echo "
<table class='boireaus boireaus_alt sortable resizable'>
	<thead>
		<tr>
			<th>Catégorie</th>
			<th>Action</th>
			<th>Description</th>
			<th>Date</th>
			<th>Présence</th>
		</tr>
	</thead>
	<tbody>";
while($lig=mysqli_fetch_object($res)) {
	echo "
		<tr>
			<td title=\"".str_replace('"', "'", $tab_actions_categories[$lig->id_categorie]['description'])."\">".$tab_actions_categories[$lig->id_categorie]['nom']."</td>
			<td>".$lig->nom."</td>
			<td>".nl2br($lig->description)."</td>
			<td>".formate_date($lig->date_action, 'y')."</td>
			<td>".($lig->presence=='y' ? "<img src='../images/enabled.png' class='icone20' title=\"Pointé(e) présent le ".formate_date($lig->date_pointage, 'y')." par ".civ_nom_prenom($lig->login_pointage)."\" />" : "")."</td>
		</tr>";
}
echo "
	</tbody>
</table>";
*/
require("../lib/footer.inc.php");
