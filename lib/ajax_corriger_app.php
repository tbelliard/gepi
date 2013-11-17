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

@set_time_limit(0);

// On indique qu'il faut creer des variables non protégées (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

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

//INSERT INTO droits SET id='/lib/ajax_corriger_app.php',administrateur='F',professeur='V',cpe='F',scolarite='F',eleve='F',responsable='F',secours='F',autre='V',description='Correction appreciation',statut='';
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

check_token();

header('Content-Type: text/html; charset=utf-8');

//debug_var();

$corriger_app_login_eleve=isset($_POST['corriger_app_login_eleve']) ? $_POST['corriger_app_login_eleve'] : "";
$corriger_app_id_groupe=isset($_POST['corriger_app_id_groupe']) ? $_POST['corriger_app_id_groupe'] : "";
$corriger_app_id_classe=isset($_POST['corriger_app_id_classe']) ? $_POST['corriger_app_id_classe'] : "";
$corriger_app_num_periode=isset($_POST['corriger_app_num_periode']) ? $_POST['corriger_app_num_periode'] : "";

$app=isset($NON_PROTECT['app']) ? traitement_magic_quotes($NON_PROTECT['app']) : "";

/*
$f=fopen("/tmp/debug_mail_corriger_app_faute.txt","a+");
fwrite($f,"========================================"."\n");
fwrite($f,"++++++++++++++++++++++++++++++++++++++++"."\n");
fwrite($f,"========================================"."\n");
fwrite($f,strftime("%Y%m%d à %H%M%S")."\n");
fwrite($f,$corriger_app_message."\n");

fwrite($f,"========================================"."\n");
fwrite($f,strftime("%Y%m%d à %H%M%S")."\n");
fwrite($f,$corriger_app_message."\n");
*/

//$app=preg_replace("/\\\\n/","\n",$app);
//$app=stripslashes($app);

/*
fwrite($f,"========================================"."\n");
fwrite($f,$corriger_app_message."\n");
fwrite($f,"========================================"."\n");
fclose($f);
*/

//if(!is_numeric($corriger_app_id_groupe)) {
//}

if(($corriger_app_login_eleve=='')||($corriger_app_id_groupe=='')||(!is_numeric($corriger_app_id_groupe))||($corriger_app_id_groupe==0)||($app=='')) {
	echo "<span style='color:red'> KO</span>";
	return false;
	die();
}

if (!(check_prof_groupe($_SESSION['login'],$corriger_app_id_groupe))) {
	echo "<span style='color:red'> KO</span>";
	return false;
	die();
}

$current_group=get_group($corriger_app_id_groupe);


// La période est-elle ouverte?
if (in_array($corriger_app_login_eleve, $current_group["eleves"][$corriger_app_num_periode]["list"])) {
	$eleve_id_classe = $current_group["classes"]["classes"][$current_group["eleves"][$corriger_app_num_periode]["users"][$corriger_app_login_eleve]["classe"]]["id"];

	if($current_group["classe"]["ver_periode"][$eleve_id_classe][$corriger_app_num_periode]=="N") {

		// Contrôle des saisies pour supprimer les sauts de lignes surnuméraires.
		$app=suppression_sauts_de_lignes_surnumeraires($app);

		//=========================
		// Ménage: pour ne pas laisser une demande de validation de correction alors qu'on a rouvert la période en saisie... on risquerait d'écraser par la suite l'enregistrement fait après la rouverture de période.
		$sql="DELETE FROM matieres_app_corrections WHERE (login='$corriger_app_login_eleve' AND id_groupe='".$current_group["id"]."' AND periode='$corriger_app_num_periode');";
		$del=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		//=========================

		$test_eleve_app_query = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM matieres_appreciations WHERE (login='$corriger_app_login_eleve' AND id_groupe='" . $current_group["id"]."' AND periode='$corriger_app_num_periode')");
		$test = mysqli_num_rows($test_eleve_app_query);
		if ($test != "0") {
			if ($app != "") {
				$register = mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE matieres_appreciations SET appreciation='" . $app . "' WHERE (login='$corriger_app_login_eleve' AND id_groupe='" . $current_group["id"]."' AND periode='$corriger_app_num_periode')");
			} else {
				$register = mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM matieres_appreciations WHERE (login='$corriger_app_login_eleve' AND id_groupe='" . $current_group["id"]."' AND periode='$corriger_app_num_periode')");
			}

			if (!$register) {
				echo "<span style='color:red'> KO</span>";
				return false;
				die();
			}
			else {
				echo stripslashes(nl2br($app));
				die();
			}

		} else {
			if ($app != "") {
				$register = mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO matieres_appreciations SET login='$corriger_app_login_eleve',id_groupe='" . $current_group["id"]."',periode='$corriger_app_num_periode',appreciation='" . $app . "'");

				if (!$register) {
					echo "<span style='color:red'> KO</span>";
					return false;
					die();
				}
				else {
					echo stripslashes(nl2br($app));
					die();
				}
			}
		}
	}
}
?>
