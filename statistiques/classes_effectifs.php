<?php
/*
 *
 *
 * Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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

$sql="SELECT 1=1 FROM droits WHERE id='/statistiques/classes_effectifs.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/statistiques/classes_effectifs.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Export de données des bulletins',
statut='';";
$insert=mysql_query($sql);
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}


/*
function clean_string_csv($texte) {
	// Pour remplacer les ; par ., et les " par '' et virer les retours à la ligne
	$texte=preg_replace("/;/",".,",$texte);
	$texte=preg_replace('/"/',"''",$texte);
	$texte=preg_replace('/\\\r\\\n/','',$texte);
	return $texte;
}
*/


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
	if($_GET['export_csv']=='effectifs') {
		$nom_fic = "export_classes_effectifs_".date("Ymd_His").".csv";

		$csv="Classes;Effectifs;\r\n";
		for($i=0;$i<count($tab_classe);$i++) {
			$csv.=$tab_classe[$i]['classe'].";";
			$sql="SELECT e.login FROM j_eleves_classes jec, eleves e WHERE jec.id_classe='".$tab_classe[$i]['id']."' AND e.login=jec.login AND jec.periode='1';";
			$res_eff=mysql_query($sql);
			$csv.=mysql_num_rows($res_eff).";\r\n";
		}
		send_file_download_headers('text/x-csv',$nom_fic);
		//echo $csv;
		echo echo_csv_encoded($csv);
		die();
	}
	elseif($_GET['export_csv']=='effectifs_sexe') {

		$nom_fic = "export_classes_effectifs_sexe_".date("Ymd_His").".csv";
		$csv="Classes;Effectifs;\r\n";
		for($i=0;$i<count($tab_classe);$i++) {
			$csv.=$tab_classe[$i]['classe'].";";

			$sql="SELECT e.login FROM j_eleves_classes jec, eleves e WHERE jec.id_classe='".$tab_classe[$i]['id']."' AND e.login=jec.login AND jec.periode='1' AND e.sexe='M';";
			//echo "$sql<br />\n";
			$res_eff=mysql_query($sql);
			$csv.=mysql_num_rows($res_eff).";";
		
			$sql="SELECT e.login FROM j_eleves_classes jec, eleves e WHERE jec.id_classe='".$tab_classe[$i]['id']."' AND e.login=jec.login AND jec.periode='1' AND e.sexe='F';";
			//echo "$sql<br />\n";
			$res_eff=mysql_query($sql);
			$csv.=mysql_num_rows($res_eff).";";
		
			$sql="SELECT e.login FROM j_eleves_classes jec, eleves e WHERE jec.id_classe='".$tab_classe[$i]['id']."' AND e.login=jec.login AND jec.periode='1';";
			//echo "$sql<br />\n";
			$res_eff=mysql_query($sql);
			$csv.=mysql_num_rows($res_eff).";\r\n";
		}
		send_file_download_headers('text/x-csv',$nom_fic);
		//echo $csv;
		echo echo_csv_encoded($csv);
		die();
	}
}


// ===================== entete Gepi ======================================//
$titre_page = "Classes, effectifs,...";
require_once("../lib/header.inc.php");
// ===================== fin entete =======================================//

//debug_var();

echo "<p class='bold'><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

if($nb_classes==0) {
	echo "<p style='color:red'>Aucune classe n'existe encore.</p>\n";

	require_once("../lib/footer.inc.php");
	die();
}

echo "<p>Effectifs en période 1&nbsp;: <a href='".$_SERVER['PHP_SELF']."?export_csv=effectifs'>Export CSV</a></p>\n";
echo "<table class='boireaus'>\n";
echo "<tr>\n";
echo "<th>Classes</th>\n";
echo "<th>Effectifs</th>\n";
echo "</tr>\n";
$alt=1;
for($i=0;$i<count($tab_classe);$i++) {
	$alt=$alt*(-1);
	echo "<tr class='lig$alt white_hover'>\n";
	echo "<td>".$tab_classe[$i]['classe']."</td>\n";

	echo "<td>";
	$sql="SELECT e.login FROM j_eleves_classes jec, eleves e WHERE jec.id_classe='".$tab_classe[$i]['id']."' AND e.login=jec.login AND jec.periode='1';";
	//echo "$sql<br />\n";
	$res_eff=mysql_query($sql);
	echo mysql_num_rows($res_eff);
	echo "</td>\n";
	echo "</tr>\n";
}
echo "</table>\n";

echo "<p>Effectifs par sexe en période 1&nbsp;: <a href='".$_SERVER['PHP_SELF']."?export_csv=effectifs_sexe'>Export CSV</a></p>\n";
echo "<table class='boireaus'>\n";
echo "<tr>\n";
echo "<th>Classes</th>\n";
echo "<th>Effectifs garçons</th>\n";
echo "<th>Effectifs filles</th>\n";
echo "<th>Effectifs totaux</th>\n";
echo "</tr>\n";
$alt=1;
for($i=0;$i<count($tab_classe);$i++) {
	$alt=$alt*(-1);
	echo "<tr class='lig$alt white_hover'>\n";
	echo "<td>".$tab_classe[$i]['classe']."</td>\n";

	echo "<td>";
	$sql="SELECT e.login FROM j_eleves_classes jec, eleves e WHERE jec.id_classe='".$tab_classe[$i]['id']."' AND e.login=jec.login AND jec.periode='1' AND e.sexe='M';";
	//echo "$sql<br />\n";
	$res_eff=mysql_query($sql);
	echo mysql_num_rows($res_eff);
	echo "</td>\n";

	echo "<td>";
	$sql="SELECT e.login FROM j_eleves_classes jec, eleves e WHERE jec.id_classe='".$tab_classe[$i]['id']."' AND e.login=jec.login AND jec.periode='1' AND e.sexe='F';";
	//echo "$sql<br />\n";
	$res_eff=mysql_query($sql);
	echo mysql_num_rows($res_eff);
	echo "</td>\n";

	echo "<td>";
	$sql="SELECT e.login FROM j_eleves_classes jec, eleves e WHERE jec.id_classe='".$tab_classe[$i]['id']."' AND e.login=jec.login AND jec.periode='1';";
	//echo "$sql<br />\n";
	$res_eff=mysql_query($sql);
	echo mysql_num_rows($res_eff);
	echo "</td>\n";

	echo "</tr>\n";
}
echo "</table>\n";

/*
$sql="SELECT COUNT(e.login) AS nb_filles FROM j_eleves_classes jec, eleves e WHERE jec.id_classe='XXX' AND e.login=jec.login AND e.sexe='F';";
echo "$sql<br />\n";

$sql="SELECT COUNT(e.login) AS nb_garcons FROM j_eleves_classes jec, eleves e WHERE jec.id_classe='XXX' AND e.login=jec.login AND e.sexe='M';";
echo "$sql<br />\n";

$sql="SELECT COUNT(e.login) AS nb_filles FROM j_eleves_classes jec, eleves e WHERE e.login=jec.login AND e.sexe='F';";
echo "$sql<br />\n";

$sql="SELECT COUNT(e.login) AS nb_garcons FROM j_eleves_classes jec, eleves e WHERE e.login=jec.login AND e.sexe='M';";
echo "$sql<br />\n";
*/
// Pour afficher au-dessus du photocopieur:...

echo "<p><em>NOTES&nbsp;:</em> Certains de ces tableaux peuvent par exemple servir pour un affichage des effectifs au-dessus du photocopieur.</p>\n";

require_once("../lib/footer.inc.php");
?>
