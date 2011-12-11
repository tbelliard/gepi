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
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}



$id_groupe = isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL);
$id_classe = isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);

$periode_num = isset($_GET['periode_num']) ? $_GET['periode_num'] : 0;

if (!is_numeric($periode_num)) $periode_num = 0;

if (is_numeric($id_groupe) && $id_groupe > 0) {
	$current_group = get_group($id_groupe);
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

if(isset($_GET['ligne_entete'])){
	switch($_GET['mode']){
		case "Id_Note_App":
			$fd.="IDENTIFIANT;NOTE;APPRECIATION\n";
			break;
		case "Id_App":
			$fd.="IDENTIFIANT;APPRECIATION\n";
			break;
		case "Nom_Prenom_Id_App":
			$fd.="NOM;PRENOM;IDENTIFIANT;APPRECIATION\n";
			break;
		case "Nom_Prenom_Id":
			$fd.="NOM;PRENOM;IDENTIFIANT\n";
			break;
		case "Nom_Prenom_Id_Note_App":
			$fd.="NOM;PRENOM;IDENTIFIANT;NOTE;APPRECIATION\n";
			break;
	}
}

if ($current_group) {
	foreach($current_group["eleves"][$periode_num]["users"] as $current_eleve) {
		$eleve_login = $current_eleve["login"];
		$eleve_nom = $current_eleve["nom"];
		$eleve_prenom = $current_eleve["prenom"];

		/*
		//if ($_GET['champs'] == 3) $fd.="\"".$eleve_nom."\",\"".$eleve_prenom."\",\"".$eleve_login."\""."\n";
		if ($_GET['champs'] == 3) $fd.=$eleve_nom.";".$eleve_prenom.";".$eleve_login."\n";
		//if ($_GET['champs'] == 1) $fd.=$eleve_login."\n";
		if ($_GET['champs'] == 1){
			if(isset($_GET['ligne_entete'])){
				$fd.=$eleve_login.";;\n";
			}
			else{
				$fd.=$eleve_login."\n";
			}
		}
		*/

		$sql="SELECT note,statut FROM matieres_notes WHERE login='$eleve_login' AND periode='$periode_num' AND id_groupe='$id_groupe'";
		//echo "$sql<br />\n";
		$res_note=mysql_query($sql);
		if(mysql_num_rows($res_note)){
			$lig_note=mysql_fetch_object($res_note);
			if($lig_note->statut==''){
				$note=my_ereg_replace("\.",",",$lig_note->note);
			}
			else{
				$note=$lig_note->statut;
			}
		}
		else{
			$note="-";
		}


		$app="-";
		$sql="SELECT appreciation FROM matieres_appreciations WHERE login='$eleve_login' AND periode='$periode_num' AND id_groupe='$id_groupe'";
		$res_appreciation=mysql_query($sql);
		if(mysql_num_rows($res_appreciation)){
			$lig_appreciation=mysql_fetch_object($res_appreciation);
			//$app=my_ereg_replace("\n"," ",$lig_appreciation->appreciation);
			$app=my_ereg_replace("\n"," ",my_ereg_replace("\r","",strtr($lig_appreciation->appreciation,";",".")));
			//$app=strtr($lig_appreciation->appreciation,"\n"," ");
			//$app="grr".my_eregi_replace("<br />"," ",my_eregi_replace("<br>"," ",nl2br($lig_appreciation->appreciation)));
		}

		switch($_GET['mode']){
			case "Id_Note_App":
				//$fd.=$eleve_login.";".";"."\n";
				$fd.=$eleve_login.";".$note.";".$app."\n";
				break;
			case "Id_App":
				//$fd.=$eleve_login.";"."\n";
				$fd.=$eleve_login.";".$app."\n";
				break;
			case "Nom_Prenom_Id_App":
				//$fd.=$eleve_nom.";".$eleve_prenom.";".$eleve_login.";"."\n";
				$fd.=$eleve_nom.";".$eleve_prenom.";".$eleve_login.";".$app."\n";
				break;
			case "Nom_Prenom_Id":
				$fd.=$eleve_nom.";".$eleve_prenom.";".$eleve_login."\n";
				break;
			case "Nom_Prenom_Id_Note_App":
				//$fd.=$eleve_nom.";".$eleve_prenom.";".$eleve_login.";".";"."\n";
				$fd.=$eleve_nom.";".$eleve_prenom.";".$eleve_login.";".$note.";".$app."\n";
				break;
		}
	}
} else {
	// Cas où on demande un fichier pour importation d'appréciations du conseil
	$appel_donnees_eleves = mysql_query("SELECT DISTINCT e.*
		FROM eleves e, j_eleves_classes j
		WHERE (
		j.id_classe='".$id_classe."' AND
		j.login = e.login AND
		periode='".$periode_num."'
		) ORDER BY nom, prenom");
	$nombre_lignes = mysql_num_rows($appel_donnees_eleves);
	$i = 0;
	while($i < $nombre_lignes) {
		$eleve_login = mysql_result($appel_donnees_eleves, $i, "login");
		$eleve_nom = mysql_result($appel_donnees_eleves, $i, "nom");
		$eleve_prenom = mysql_result($appel_donnees_eleves, $i, "prenom");
		$k=1;
		$enr_eleve = 'no';
		if ($_SESSION['statut'] != 'scolarite') {
			$eleve_profsuivi_query = mysql_query("SELECT * FROM  j_eleves_professeurs
								WHERE (
								login='".$eleve_login."' AND
								professeur='".$_SESSION['login']."' AND
								id_classe = '".$id_classe."')");
			$test_suivi = mysql_num_rows($eleve_profsuivi_query);
		}
		else{
			$test_suivi = 1;
		}
		if ($test_suivi != "0")  {
			/*
			//if ($_GET['champs'] == 3) $fd.="\"".$eleve_nom."\",\"".$eleve_prenom."\",\"".$eleve_login."\""."\n";
			if ($_GET['champs'] == 3) $fd.=$eleve_nom.";".$eleve_prenom.";".$eleve_login."\n";
			//if ($_GET['champs'] == 1) $fd.=$eleve_login."\n";
			if ($_GET['champs'] == 1){
				if(isset($_GET['ligne_entete'])){
					$fd.=$eleve_login.";;\n";
				}
				else{
					$fd.=$eleve_login."\n";
				}
			}*/


			switch($_GET['mode']){
				case "Id_Note_App":
					$fd.=$eleve_login.";".";"."\n";
					break;
				case "Id_App":
					$fd.=$eleve_login.";"."\n";
					break;
				case "Nom_Prenom_Id_App":
					$fd.=$eleve_nom.";".$eleve_prenom.";".$eleve_login.";"."\n";
					break;
				case "Nom_Prenom_Id":
					$fd.=$eleve_nom.";".$eleve_prenom.";".$eleve_login."\n";
					break;
				case "Nom_Prenom_Id_Note_App":
					$fd.=$eleve_nom.";".$eleve_prenom.";".$eleve_login.";".";"."\n";
					break;
			}
		}
		$i++;
	}
}

//echo $fd;
echo echo_csv_encoded($fd);

?>