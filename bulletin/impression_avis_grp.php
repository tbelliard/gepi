<?php
/*
 *
 * Copyright 2001, 2016 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

//INSERT INTO droits VALUES ('/saisie/impression_avis.php', 'F', 'V', 'F', 'V', 'F', 'F', 'F','Impression des avis trimestrielles des conseils de classe.', '');

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

//**************** EN-TETE *****************
$titre_page = "Impression des avis sur les groupes-classes";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

echo "<p class=bold><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
echo "</p>\n";

echo "<h3>Liste des classes : </h3>\n";

echo "<p>Séléctionnez la classe et la période pour lesquels vous souhaitez imprimer les avis :</p>\n";

$sql=retourne_sql_mes_classes();
$result_classes=mysqli_query($GLOBALS["mysqli"], $sql);
$nb_classes = mysqli_num_rows($result_classes);

if(mysqli_num_rows($result_classes)==0){
	echo "<p style='color:red'>Il semble qu'aucune classe ne vous soit associée.</p>\n";
	require("../lib/footer.inc.php");
}

$nb_classes=mysqli_num_rows($result_classes);
$nb_class_par_colonne=round($nb_classes/3);
echo "<table width='100%'>\n";
echo "<tr valign='top' align='left'>\n";
$cpt=0;
//echo "<td style='padding: 0 10px 0 10px'>\n";
echo "<td>\n";
echo "<table border='1' class='boireaus boireaus_alt'>\n";
while($lig_class=mysqli_fetch_object($result_classes)){
	if(($cpt>0)&&(round($cpt/$nb_class_par_colonne)==$cpt/$nb_class_par_colonne)){
		echo "</table>\n";
		echo "</td>\n";
		//echo "<td style='padding: 0 10px 0 10px'>\n";
		echo "<td>\n";
		echo "<table border='1' class='boireaus boireaus_alt'>\n";
	}

	$sql="SELECT num_periode,nom_periode FROM periodes WHERE id_classe='$lig_class->id' ORDER BY num_periode";
	$res_per=mysqli_query($GLOBALS["mysqli"], $sql);

	if(mysqli_num_rows($res_per)==0){
		echo "<p>ERREUR: Aucune période n'est définie pour la classe $lig_class->classe</p>\n";
		echo "</body></html>\n";
		die();
	}
	else{
		echo "<tr>\n";
		echo "<th>$lig_class->classe</th>\n";
		while($lig_per=mysqli_fetch_object($res_per)){
/*    $_POST['mode_bulletin']=	pdf
    $_POST['type_bulletin']=	-1
    $_POST['bull_pdf_debug']=	y
    $_POST['choix_periode_num']=	fait
    $_POST['b_adr_pg']=	xx
    $_POST['intercaler_app_classe']=	y
    $_POST['bouton_valide_select_eleves1']=	Valider
    $_POST['tab_id_classe']=	Array (*)
    $_POST[tab_id_classe]['0']=	33
    $_POST['tab_periode_num']=	Array (*)
    $_POST[tab_periode_num]['0']=	2
    $_POST['valide_select_eleves']=	y
*/

			echo "<td> - <a href='bull_index.php?mode_bulletin=pdf&amp;type_bulletin=-1&amp;choix_periode_num=fait&amp;b_adr_pg=xx&amp;intercaler_app_classe=y&amp;bouton_valide_select_eleves1=Valider&amp;tab_id_classe[0]=$lig_class->id&amp;tab_periode_num[0]=".$lig_per->num_periode."&amp;valide_select_eleves=y' target='_blank'>".$lig_per->nom_periode."</a></td>\n";
		}
		echo "<td> - <a href='bull_index.php?mode_bulletin=pdf&amp;type_bulletin=-1&amp;choix_periode_num=fait&amp;b_adr_pg=xx&amp;intercaler_app_classe=y&amp;bouton_valide_select_eleves1=Valider&amp;tab_id_classe[0]=$lig_class->id";
		for($loop=0;$loop<mysqli_num_rows($res_per);$loop++) {
			echo "&amp;tab_periode_num[$loop]=".($loop+1);
		}
		echo "&amp;valide_select_eleves=y' target='_blank'>Toutes</a></td>\n";
		echo "</tr>\n";
	}
	$cpt++;
}
echo "</table>\n";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";

require("../lib/footer.inc.php");
?>
