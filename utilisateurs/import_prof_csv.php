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
$avec_adresse=isset($_GET['avec_adresse']) ? $_GET['avec_adresse'] : "n";

$tab_statut=array('professeur', 'administrateur', 'scolarite', 'cpe', 'secours', 'autre', 'responsable', 'eleve', 'personnels');

if(!in_array($export_statut, $tab_statut)) {
	header("Location: index.php?mode=personnels&msg=".rawurlencode('Statut inconnu'));
	die();
}

$nom_fic = "base_".$export_statut."_gepi.csv";
send_file_download_headers('text/x-csv',$nom_fic);

$fd = '';

//$appel_donnees = mysql_query("SELECT * FROM utilisateurs ORDER BY nom, prenom");
if($export_statut=='personnels') {
	$sql="SELECT * FROM utilisateurs WHERE statut!='eleve' AND statut!='responsable' AND etat='actif' ORDER BY statut, nom, prenom;";

	if(!isset($_GET['sans_entete'])) {
		$fd.="NOM;PRENOM;LOGIN;EMAIL;STATUT";
		$fd.="\n";
	}
}
else {
	$sql="SELECT * FROM utilisateurs WHERE statut='$export_statut' AND etat='actif' ORDER BY statut, nom, prenom;";

	if(!isset($_GET['sans_entete'])) {
		$fd.="NOM;PRENOM;LOGIN;EMAIL";
		if($export_statut=='responsable') {
			$fd.=";ENFANTS;SEXE;IDENTIFIANT;STATUT";
			if($avec_adresse=='y') {
				$fd.=";ADR1;ADR2;ADR3;ADR4;CODE_POSTAL;COMMUNE;PAYS";
			}
		}
		$fd.="\n";
	}
}
//echo "$sql<br />";
$appel_donnees = mysqli_query($GLOBALS["mysqli"], $sql);
$nombre_lignes = mysqli_num_rows($appel_donnees);

//echo "\$nombre_lignes=$nombre_lignes<br />";

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

		// Ajout d'infos:
		$sql="SELECT pers_id, civilite FROM resp_pers WHERE login='$user_login';";
		$res_pers_id=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_pers_id)==1) {
			$civ=mysql_result($res_pers_id, 0, 'civilite');
			if(($civ=='Mme')||($civ=='Mlle')) {
				$fd.=";F";
			}
			else {
				$fd.=";M";
			}

			$pers_id=mysql_result($res_pers_id, 0, 'pers_id');
			$fd.=";R".$pers_id;
		}
		else {
			$fd.=";;";
		}

		$fd.=";".$user_statut;

		if($avec_adresse=='y') {
			$sql="SELECT * FROM resp_adr ra, resp_pers rp WHERE rp.adr_id=ra.adr_id AND rp.login='$user_login';";
			$res_adr=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_adr)==0) {
				$fd.=";".";".";".";".";".";".";";
			}
			else {
				$lig_adr=mysqli_fetch_object($res_adr);
				$fd.=";".strtr($lig_adr->adr1,";",",").";".strtr($lig_adr->adr2,";",",").";".strtr($lig_adr->adr3,";",",").";".strtr($lig_adr->adr4,";",",").";".strtr($lig_adr->cp,";",",").";".strtr($lig_adr->commune,";",",").";".strtr($lig_adr->pays,";",",");
				
			}
		}
	}
	$fd.=";\n";
	$j++;
}

//echo $fd;

echo echo_csv_encoded($fd);
?>
