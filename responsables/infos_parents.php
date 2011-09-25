<?php
/*
 *
 * @version $Id$
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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

$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

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

$sql="SELECT 1=1 FROM droits WHERE id='/responsables/infos_parents.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/responsables/infos_parents.php',
administrateur='V',
professeur='F',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Grille élèves/parents',
statut='';";
$insert=mysql_query($sql);
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}

$sql="SELECT DISTINCT id, classe FROM classes ORDER BY classe;";
//echo "$sql<br />\n";
$res_classes=mysql_query($sql);
$nb_classes=mysql_num_rows($res);
if($nb_classes>0) {
	$tab_classe=array();
	$cpt=0;
	while($lig_classe=mysql_fetch_object($res_classes)) {
		$tab_classe[$cpt]=array();
		$tab_classe[$cpt]['id']=$lig_classe->id;
		$tab_classe[$cpt]['classe']=$lig_classe->classe;
		$cpt++;
	}
}

if(isset($_GET['export_csv'])) {
	$nom_fic = "export_infos_parents_1_".date("Ymd_His").".csv";

	$csv="Classe;Nom;Prenom;Sexe;Naissance;Responsable;Tel_pers;Tel_port;Tel_prof;Email;Adresse;\r\n";
	for($i=0;$i<count($tab_classe);$i++) {
		$csv.=$tab_classe[$i]['classe'].";";

		$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec WHERE jec.login=e.login AND jec.id_classe='".$tab_classe[$i]['id']."' ORDER BY e.nom, e.prenom;";
		$res_ele=mysql_query($sql);
		while($lig_ele=mysql_fetch_object($res_ele)) {
			$sql="SELECT rp.* FROM resp_pers rp, responsables2 r WHERE (r.resp_legal='1' OR r.resp_legal='2') AND r.pers_id=rp.pers_id AND r.ele_id='$lig_ele->ele_id' ORDER BY r.resp_legal;";
			//echo "$sql<br />";
			$res_resp=mysql_query($sql);

			while($lig_resp=mysql_fetch_object($res_resp)) {

				$csv.=$tab_classe[$i]['classe'].";";
				$csv.=strtoupper($lig_ele->nom).";";
				$csv.=casse_mot($lig_ele->prenom,'majf2').";";
				$csv.=$lig_ele->sexe.";";
				$csv.=formate_date($lig_ele->naissance).";";

				$csv.=$lig_resp->civilite." ".strtoupper($lig_resp->nom)." ".casse_mot($lig_resp->prenom,'majf2').";";
				$csv.=$lig_resp->tel_pers.";";
				$csv.=$lig_resp->tel_port.";";
				$csv.=$lig_resp->tel_prof.";";
				$csv.=$lig_resp->mel.";";

				$sql="SELECT * FROM resp_adr WHERE adr_id='".$lig_resp->adr_id."';";
				//echo "$sql<br />";
				$res_adr=mysql_query($sql);
				if(mysql_num_rows($res_resp)>1) {
					$adresse="";
					$lig_adr=mysql_fetch_object($res_adr);
					$adresse.=$lig_adr->adr1;
					if($lig_adr->adr1!="") {
						$adresse.=" ";
					}
	
					$adresse.=$lig_adr->adr2;
					if($lig_adr->adr2!="") {
						$adresse.=" ";
					}
	
					$adresse.=$lig_adr->adr3;
					if($lig_adr->adr3!="") {
						$adresse.=" ";
					}
	
					$adresse.=$lig_adr->cp." ".$lig_adr->commune;
	
					$csv.=$adresse.";";
				}
				$csv.="\r\n";
			}
		}
	}
	send_file_download_headers('text/x-csv',$nom_fic);
	echo $csv;
	die();
}


// ===================== entete Gepi ======================================//
$titre_page = "Grille élèves/parents";
require_once("../lib/header.inc");
// ===================== fin entete =======================================//

//debug_var();

echo "<p class='bold'><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

if($nb_classes==0) {
	echo "<p style='color:red'>Aucune classe n'existe encore.</p>\n";

	require_once("../lib/footer.inc.php");
	die();
}

echo "<p>Informations élèves/parents&nbsp;: <a href='".$_SERVER['PHP_SELF']."?export_csv=export_infos_parents_1'>Export CSV</a></p>\n";
echo "<table class='boireaus'>\n";
echo "<tr>\n";
echo "<th rowspan='2'>Classe</th>\n";
echo "<th colspan='4'>Elève</th>\n";
echo "<th colspan='6'>Responsable</th>\n";
echo "</tr>\n";

echo "<tr>\n";
//echo "<th>Classe</th>\n";
echo "<th>Nom</th>\n";
echo "<th>Prénom</th>\n";
echo "<th>Sexe</th>\n";
echo "<th>Naissance</th>\n";
echo "<th>Responsable</th>\n";
echo "<th>Tel.pers</th>\n";
echo "<th>Tel.port</th>\n";
echo "<th>Tel.prof</th>\n";
echo "<th>Email</th>\n";
echo "<th>Adresse</th>\n";
echo "</tr>\n";
$alt=1;
for($i=0;$i<count($tab_classe);$i++) {
	$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec WHERE jec.login=e.login AND jec.id_classe='".$tab_classe[$i]['id']."' ORDER BY e.nom, e.prenom;";
	$res_ele=mysql_query($sql);
	while($lig_ele=mysql_fetch_object($res_ele)) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt white_hover'>\n";

		$rowspan="";
		$sql="SELECT rp.* FROM resp_pers rp, responsables2 r WHERE (r.resp_legal='1' OR r.resp_legal='2') AND r.pers_id=rp.pers_id AND r.ele_id='$lig_ele->ele_id' ORDER BY r.resp_legal;";
		//echo "$sql<br />";
		$res_resp=mysql_query($sql);
		if(mysql_num_rows($res_resp)>1) {
			$rowspan=" rowspan='".mysql_num_rows($res_resp)."'";
		}
		echo "<td$rowspan>".$tab_classe[$i]['classe']."</td>\n";
		echo "<td$rowspan>".strtoupper($lig_ele->nom)."</td>\n";
		echo "<td$rowspan>".casse_mot($lig_ele->prenom,'majf2')."</td>\n";
		echo "<td$rowspan>".$lig_ele->sexe."</td>\n";
		echo "<td$rowspan>".formate_date($lig_ele->naissance)."</td>\n";
		$cpt=0;
		while($lig_resp=mysql_fetch_object($res_resp)) {
			if($cpt>0) {
				echo "</tr>\n";
				echo "<tr class='lig$alt white_hover'>\n";
			}
			echo "<td>";
			echo $lig_resp->civilite." ".strtoupper($lig_resp->nom)." ".casse_mot($lig_resp->prenom,'majf2');
			echo "</td>\n";
			echo "<td>$lig_resp->tel_pers</td>\n";
			echo "<td>$lig_resp->tel_port</td>\n";
			echo "<td>$lig_resp->tel_prof</td>\n";
			echo "<td>$lig_resp->mel</td>\n";
			echo "<td>";
			$sql="SELECT * FROM resp_adr WHERE adr_id='".$lig_resp->adr_id."';";
			//echo "$sql<br />";
			$res_adr=mysql_query($sql);
			if(mysql_num_rows($res_resp)>1) {
				$adresse="";
				$lig_adr=mysql_fetch_object($res_adr);
				$adresse.=$lig_adr->adr1;
				if($lig_adr->adr1!="") {
					$adresse.="<br />\n";
				}

				$adresse.=$lig_adr->adr2;
				if($lig_adr->adr2!="") {
					$adresse.="<br />\n";
				}

				$adresse.=$lig_adr->adr3;
				if($lig_adr->adr3!="") {
					$adresse.="<br />\n";
				}

				$adresse.=$lig_adr->cp." ".$lig_adr->commune;

				echo $adresse;
			}
			echo "</td>\n";
			$cpt++;
		}
		echo "</tr>\n";
	}	
}
echo "</table>\n";

echo "<p><br /></p>\n";

//echo "<p><em>NOTES&nbsp;:</em></p>\n";

require_once("../lib/footer.inc.php");
?>
