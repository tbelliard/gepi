<?php
/*
*
* Copyright 2001-2012 Thomas Belliard, Stephane Boireau, Eric Lebrun
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


$sql="SELECT 1=1 FROM droits WHERE id='/mod_abs2/consultation_effectif_cantine.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_abs2/consultation_effectif_cantine.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Effectif cantine: Consultation',
statut='';";
$insert=mysql_query($sql);
}

// Check access
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$id_groupe=isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL);
$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);

$msg = '';

$sql="CREATE TABLE IF NOT EXISTS cantine (
	id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	jour DATE NOT NULL ,
	login VARCHAR( 50 ) NOT NULL ,
	id_groupe INT NOT NULL ,
	id_classe INT NOT NULL ,
	effectif TINYINT NOT NULL ,
	login_ele VARCHAR( 50 ) NOT NULL ,
	instant DATETIME NOT NULL
	) ENGINE = MYISAM ;";
$create_table=mysql_query($sql);

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
// End standart header
$titre_page = "Cantine";
if(isset ($themessage)) $messageEnregistrer = $themessage;
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

$jour=strftime("%Y-%m-%d");

$sql="SELECT * FROM edt_creneaux ORDER BY heuredebut_definie_periode;";
//echo "$sql<br />";
$res_creneau=mysql_query($sql);
if(mysql_num_rows($res_creneau)>0) {
	$tab_creneaux=array();
	$cpt=0;
	while($lig_creneau=mysql_fetch_object($res_creneau)) {
		$tab_creneaux[$cpt]['nom']=$lig_creneau->nom_definie_periode;
		$tab_creneaux[$cpt]['heuredebut']=$lig_creneau->heuredebut_definie_periode;
		$tab_creneaux[$cpt]['heurefin']=$lig_creneau->heurefin_definie_periode;

		$tab_creneaux[$cpt]['ts_debut']=mysql_date_to_unix_timestamp($jour." ".$tab_creneaux[$cpt]['heuredebut']);
		$tab_creneaux[$cpt]['ts_fin']=mysql_date_to_unix_timestamp($jour." ".$tab_creneaux[$cpt]['heurefin']);

		$cpt++;
	}

	$lignes="";
	$alt=1;
	for($i=0;$i<count($tab_creneaux);$i++) {
		$sql="SELECT * FROM cantine WHERE jour='$jour' AND instant>='".$jour." ".$tab_creneaux[$i]['heuredebut']."' AND instant<'".$jour." ".$tab_creneaux[$i]['heurefin']."' AND effectif>'-1';";
		//echo "$sql<br />";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)>0) {
			$cpt=0;
			$alt=$alt*(-1);
			$lignes.="<tr class='lig$alt white_hover'>\n";
			$lignes.="<td rowspan='".(mysql_num_rows($res)+1)."'>\n";
			$lignes.=$tab_creneaux[$i]['nom']."<br />".$tab_creneaux[$i]['heuredebut']."<br />".$tab_creneaux[$i]['heurefin']."\n";
			$lignes.="</td>\n";
			$total=0;
			$alt2=$alt;
			while($lig=mysql_fetch_object($res)) {
				if($cpt>0) {
					$alt2=$alt2*(-1);
					$lignes.="<tr class='lig$alt2 white_hover'>\n";
				}

				$lignes.="<td>\n";
				$lignes.=civ_nom_prenom($lig->login);
				$lignes.="</td>\n";

				unset($current_group);
				$lignes.="<td>\n";
				if(($lig->id_groupe!='')&&($lig->id_groupe!=0)) {
					$current_group=get_group($lig->id_groupe);
					$lignes.=$current_group['name']." (".$current_group['description'].")";
				}
				$lignes.="</td>\n";

				$lignes.="<td>\n";
				if(($lig->id_classe!='')&&($lig->id_classe!=0)) {
					$lignes.=get_nom_classe($lig->id_classe);
				}
				elseif(isset($current_group['classlist_string'])) {
					$lignes.=$current_group['classlist_string'];
				}
				$lignes.="</td>\n";

				$lignes.="<td>\n";
				$lignes.=$lig->effectif;
				$total+=$lig->effectif;
				$lignes.="</td>\n";

				$lignes.="<td>\n";
				$sql="SELECT e.nom, e.prenom FROM cantine ca, eleves e WHERE e.login=ca.login_ele AND jour='$jour' AND instant='$lig->instant' AND id_groupe='$lig->id_groupe' AND id_classe='$lig->id_classe' AND effectif='-1';";
				//echo "$sql<br />";
				$res_ele=mysql_query($sql);
				if(mysql_num_rows($res_ele)>0) {
					$cpt_ele=0;
					while($lig_ele=mysql_fetch_object($res_ele)) {
						if($cpt_ele>0) {$lignes.=", ";}
						$nom_ele=casse_mot($lig_ele->nom,'majf2');
						$prenom_ele=casse_mot($lig_ele->prenom,'majf2');
						$lignes.="<span title='$nom_ele $prenom_ele'>".$nom_ele." ".mb_substr($prenom_ele,0,1)."</span>";
						$cpt_ele++;
					}
				}
				$lignes.="</td>\n";

				$cpt++;
			}
			$lignes.="</tr>\n";

			$lignes.="<tr>\n";
			$lignes.="<th colspan='3'>Total</th>\n";
			$lignes.="<th>$total</th>\n";
			$lignes.="<th></th>\n";
			$lignes.="</tr>\n";
		}
	}

	if($lignes!="") {
		echo "<table class='boireaus' summary='Tableau des effectifs cantine'>\n";
		echo "<tr>\n";
		echo "<th>Créneau</th>\n";
		echo "<th>Saisi par</th>\n";
		echo "<th>Enseignement</th>\n";
		echo "<th>Classe(s)</th>\n";
		echo "<th>Effectif</th>\n";
		echo "<th>Élèves</th>\n";
		echo "</tr>\n";
		echo $lignes;
		echo "</table>\n";
	}
}
else {
	// Tout afficher sans s'occuper des créneaux
	echo "<p style='color:red'>Les créneaux horaire ne sont pas définis...</p>\n";
}


require("../lib/footer.inc.php");
?>
