<?php
/*
 * Last modification  : 29/11/2006
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
 * but WITHOUT ANY WARRANTY; without even the  warranty of
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
};


//INSERT INTO droits VALUES ('/groupes/get_csv.php', 'F', 'V', 'V', 'V', 'F', 'V', 'Génération de CSV élèves', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

$id_groupe = isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL);
$id_classe = isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);

//echo "1 - \$id_groupe=$id_groupe<br />";

$periode_num = isset($_GET['periode_num']) ? $_GET['periode_num'] : 0;

if (!is_numeric($periode_num)) $periode_num = 0;

if (is_numeric($id_groupe) && $id_groupe > 0) {
	$current_group = get_group($id_groupe);
	//echo "2<br />";
} else {
	$current_group = false;
}

if ($current_group) {
	$nom_fic = $current_group["name"] . "-" . $current_group["classlist_string"] . ".csv";
} else {
	$classe = mysql_result(mysql_query("SELECT classe FROM classes WHERE id = '" . $id_classe . "'"), 0);
	$nom_fic = $classe . ".csv";
}

$now = gmdate('D, d M Y H:i:s') . ' GMT';
header('Content-Type: text/x-csv');
header('Expires: ' . $now);
// lem9 & loic1: IE need specific headers
if (my_ereg('MSIE', $_SERVER['HTTP_USER_AGENT'])) {
    header('Content-Disposition: inline; filename="' . $nom_fic . '"');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
} else {
    header('Content-Disposition: attachment; filename="' . $nom_fic . '"');
    header('Pragma: no-cache');
}

include "../lib/periodes.inc.php";
$fd = '';

$fd.="CLASSE;LOGIN;NOM;PRENOM;SEXE;DATE_NAISS\n";

if($current_group) {
	foreach($current_group["eleves"][$periode_num]["users"] as $current_eleve) {
	//foreach($current_group["eleves"]["all"]["users"] as $current_eleve) {
		$eleve_login = $current_eleve["login"];
		$eleve_nom = $current_eleve["nom"];
		$eleve_prenom = $current_eleve["prenom"];

		//$eleve_classe = $current_eleve["classe"];
		$sql="SELECT classe FROM classes WHERE id='".$current_eleve["classe"]."'";
		$res_tmp=mysql_query($sql);
		if(mysql_num_rows($res_tmp)==0){
			die("$eleve_login ne serait dans aucune classe???</body></html>");
		}
		else{
			$lig_tmp=mysql_fetch_object($res_tmp);
			$eleve_classe=$lig_tmp->classe;
		}

		// La fonction get_group() dans /lib/groupes.inc.php ne récupère pas le sexe et la date de naissance...
		// ... pourrait-on l'ajouter?
		$sql="SELECT sexe,naissance FROM eleves WHERE login='$eleve_login'";
		$res_tmp=mysql_query($sql);

		if(mysql_num_rows($res_tmp)==0){
			die("Problème avec les infos de $eleve_login</body></html>");
		}
		else{
			$lig_tmp=mysql_fetch_object($res_tmp);
			$eleve_sexe=$lig_tmp->sexe;
			$eleve_naissance=$lig_tmp->naissance;
		}

		$fd.="$eleve_classe;$eleve_login;$eleve_nom;$eleve_prenom;$eleve_sexe;$eleve_naissance\n";
	}
} else {
	$appel_donnees_eleves = mysql_query("SELECT DISTINCT e.*
	    FROM eleves e, j_eleves_classes j
	    WHERE (
	    j.id_classe='".$id_classe."' AND
	    j.login = e.login AND
	    periode='".$periode_num."'
	    ) ORDER BY nom, prenom");
/*
	$appel_donnees_eleves = mysql_query("SELECT DISTINCT e.*
		FROM eleves e, j_eleves_classes j
		WHERE (
		j.id_classe='".$id_classe."' AND
		j.login = e.login
		) ORDER BY nom, prenom");
*/
	$nombre_lignes = mysql_num_rows($appel_donnees_eleves);
	$i = 0;
	while($i < $nombre_lignes) {
		$eleve_login = mysql_result($appel_donnees_eleves, $i, "login");
		$eleve_nom = mysql_result($appel_donnees_eleves, $i, "nom");
		$eleve_prenom = mysql_result($appel_donnees_eleves, $i, "prenom");
		$eleve_sexe = mysql_result($appel_donnees_eleves, $i, "sexe");
		$eleve_naissance = mysql_result($appel_donnees_eleves, $i, "naissance");

		$fd.="$classe;$eleve_login;$eleve_nom;$eleve_prenom;$eleve_sexe;$eleve_naissance\n";

		$i++;
	}
}
echo $fd;
?>