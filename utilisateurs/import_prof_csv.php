<?php
/*
 * $Id: import_prof_csv.php 6499 2011-02-12 20:53:19Z crob $
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

//include "../lib/periodes.inc.php";

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

$export_statut=isset($_GET['export_statut']) ? $_GET['export_statut'] : "";

$tab_statut=array('professeur', 'administrateur', 'scolarite', 'cpe', 'secours', 'autre', 'responsable', 'eleve', 'personnels');

if(!in_array($export_statut, $tab_statut)) {
	header("Location: index.php?mode=personnels&msg=".rawurlencode('Statut inconnu'));
	die();
}

$nom_fic = "base_".$export_statut."_gepi.csv";

$fd = '';

//$appel_donnees = mysql_query("SELECT * FROM utilisateurs ORDER BY nom, prenom");
if($export_statut=='personnels') {
	$sql="SELECT * FROM utilisateurs WHERE statut!='eleve' AND statut!='responsable' AND etat='actif' ORDER BY statut, nom, prenom;";
}
else {
	$sql="SELECT * FROM utilisateurs WHERE statut='$export_statut' AND etat='actif' ORDER BY statut, nom, prenom;";
}
//echo "$sql<br />";
$appel_donnees = mysql_query($sql);
$nombre_lignes = mysql_num_rows($appel_donnees);

$j= 0;
while($j< $nombre_lignes) {
	$user_login = mysql_result($appel_donnees, $j, "login");
	$user_nom = mysql_result($appel_donnees, $j, "nom");
	$user_prenom = mysql_result($appel_donnees, $j, "prenom");
	$user_email = mysql_result($appel_donnees, $j, "email");
	$user_statut = mysql_result($appel_donnees, $j, "statut");
	$fd.=$user_nom.";".$user_prenom.";".$user_login.";".$user_email;
	if($export_statut=='personnels') {$fd.=";".$user_statut;}
	elseif($export_statut=='responsable') {
		$liste_enfants="";
		$tmp_tab_enfants=get_enfants_from_resp_login($user_login,"avec_classe");
		for($i=1;$i<count($tmp_tab_enfants);$i+=2) {
			if($i>1) {$liste_enfants.=", ";}
			$liste_enfants.=$tmp_tab_enfants[$i];
		}
		$fd.=";".$liste_enfants;
	}
	$fd.=";\n";
	$j++;
}
send_file_download_headers('text/x-csv',$nom_fic);
echo $fd;
?>
