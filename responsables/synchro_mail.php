<?php
/*
 *
 * Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

$sql="SELECT 1=1 FROM droits WHERE id='/responsables/synchro_mail.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/responsables/synchro_mail.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Synchronisation des mail responsables',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}


if(!isset($msg)){
	$msg="";
}

$suppr_infos_actions_diff_mail=isset($_GET['suppr_infos_actions_diff_mail']) ? $_GET['suppr_infos_actions_diff_mail'] : "n";

if((isset($_GET['synchroniser']))&&($_GET['synchroniser']=='y')) {
	check_token();

	$sql="SELECT u.*, rp.mel, rp.pers_id FROM utilisateurs u, resp_pers rp WHERE rp.login=u.login AND u.statut='responsable' AND u.email!=rp.mel ORDER BY rp.nom, rp.prenom;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		$msg="Toutes les adresses mail responsables sont déjà synchronisées entre les tables 'resp_pers' et 'utilisateurs'.<br />\n";
	}
	else {
		$cpt=0;
		$erreur=0;
		if(getSettingValue('mode_email_resp')=='sconet') {
			while($lig=mysqli_fetch_object($res)) {
				$sql="UPDATE utilisateurs SET email='$lig->mel' WHERE login='$lig->login' AND statut='responsable';";
				$update=mysqli_query($GLOBALS["mysqli"], $sql);
				if($update) {
					$cpt++;
				}
				else {
					$erreur++;
				}
			}
		}
		elseif(getSettingValue('mode_email_resp')=='mon_compte') {
			while($lig=mysqli_fetch_object($res)) {
				$sql="UPDATE resp_pers SET mel='$lig->email' WHERE login='$lig->login';";
				$update=mysqli_query($GLOBALS["mysqli"], $sql);
				if($update) {
					$cpt++;
				}
				else {
					$erreur++;
				}
			}
		}

		if($cpt==0) {
			$msg="Aucune adresse n'a été mise à jour.<br />";
		}
		elseif($cpt==1) {
			$msg="Une adresse a été mise à jour.<br />";
			$suppr_infos_actions_diff_mail="y";
		}
		else {
			$msg="$cpt adresses ont été mises à jour.<br />";
			$suppr_infos_actions_diff_mail="y";
		}

		if($erreur==1) {
			$msg.="Une erreur s'est produite.<br />";
		}
		elseif($erreur>1) {
			$msg.="$erreur erreurs se sont produites.<br />";
		}
	}
}

//if((isset($_GET['suppr_infos_actions_diff_mail']))&&($_GET['suppr_infos_actions_diff_mail']=='y')) {
if($suppr_infos_actions_diff_mail=='y') {
	check_token();

	$sql="select * from infos_actions where titre like 'Adresse mail non synchro pour%' and description like '%adresse email renseignée par la personne via%';";
	$test_infos_actions=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test_infos_actions)>0) {
		$sql="delete from infos_actions where titre like 'Adresse mail non synchro pour%' and description like '%adresse email renseignée par la personne via%';";
		$del=mysqli_query($GLOBALS["mysqli"], $sql);
		if(!$del) {
			$msg.="ERREUR lors de la suppression des signalements de différence de mail en page d'accueil.<br />\n";
		}
		else {
			$msg.="Suppression des signalements de différence de mail en page d'accueil effectuée.<br />\n";
		}
	}
	else {
		$msg.="Aucun signalement n'existait en page d'accueil.<br />\n";
	}
}

//**************** EN-TETE *******************************
$titre_page = "Synchronisation des adresses mail responsables";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE ***************************

//debug_var();

if(!getSettingValue('conv_new_resp_table')){
	$sql="SELECT 1=1 FROM responsables";
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test)>0){
		echo "<p>Une conversion des données responsables est requise.</p>\n";
		echo "<p>Suivez ce lien: <a href='conversion.php'>CONVERTIR</a></p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$sql="SHOW COLUMNS FROM eleves LIKE 'ele_id'";
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test)==0){
		echo "<p>Une conversion des données élèves/responsables est requise.</p>\n";
		echo "<p>Suivez ce lien: <a href='conversion.php'>CONVERTIR</a></p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	else{
		$sql="SELECT 1=1 FROM eleves WHERE ele_id=''";
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)>0){
			echo "<p>Une conversion des données élèves/responsables est requise.</p>\n";
			echo "<p>Suivez ce lien: <a href='conversion.php'>CONVERTIR</a></p>\n";
			require("../lib/footer.inc.php");
			die();
		}
	}
}

?>
<p class='bold'><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>

<?php
	$sql="select * from infos_actions where titre like 'Adresse mail non synchro pour%' and description like '%adresse email renseignée par la personne via%';";
	$test_infos_actions=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test_infos_actions)>0) {
		echo " | <a href='".$_SERVER['PHP_SELF']."?suppr_infos_actions_diff_mail=y".add_token_in_url()."'>Supprimer les signalements de différences en page d'accueil</a>";
	}
	echo "</p>\n";

	$sql="SELECT u.*, rp.mel, rp.pers_id FROM utilisateurs u, resp_pers rp WHERE rp.login=u.login AND u.statut='responsable' AND u.email!=rp.mel ORDER BY rp.nom, rp.prenom;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p>Toutes les adresses mail responsables sont synchronisées entre les tables 'resp_pers' et 'utilisateurs'.</p>\n";

		require("../lib/footer.inc.php");
		die();
	}

	echo "<p>".mysqli_num_rows($res)." adresses mail responsables diffèrent entre les tables 'resp_pers' et 'utilisateurs'.</p>\n";

	echo "<table class='boireaus' summary='Tableau des différences'>\n";
	echo "<tr>\n";
	echo "<th>Nom</th>\n";
	echo "<th>Prenom</th>\n";
	echo "<th>Email utilisateur<br />(<i>Gérer mon compte</i>)</th>\n";
	echo "<th>Email resp_pers<br />(<i>Sconet,...</i>)</th>\n";
	echo "</tr>\n";
	$alt=1;
	while($lig=mysqli_fetch_object($res)) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt white_hover'>\n";
		echo "<td><a href='modify_resp.php?pers_id=$lig->pers_id'>$lig->nom</a></td>\n";
		echo "<td>$lig->prenom</td>\n";
		echo "<td>$lig->email</td>\n";
		echo "<td>$lig->mel</td>\n";
		echo "</tr>\n";
	}
	echo "</table>\n";

	echo "<p>Le paramétrage de la synchronisation est actuellement&nbsp;: <span style='color:green'>".getSettingValue('mode_email_resp')."</span><br />";

	if(getSettingValue('mode_email_resp')=="mon_compte") {
		echo "Cela signifie que c'est ce qui est saisi par le responsable dans <span class='bold'>Gérer mon compte</span> qui prime sur ce qui est saisi dans Sconet (<em>ou plus généralement dans Gestion des responsables</em>).";
	}
	elseif(getSettingValue('mode_email_resp')=='sconet') {
		echo "Cela signifie que c'est ce qui est saisi <span class='bold'>dans Sconet</span> ou <span class='bold'>dans Gepi dans Gestion des bases/Gestion des responsables</span> prime sur ce qui est saisi par le responsable dans <span class='bold'>Gérer mon compte</span>.";
	}
	echo "</p>\n";

	if(getSettingValue('mode_email_resp')=='sconet') {
		echo "<p>Pour mettre à jour les email des comptes d'utilisateurs d'après les valeurs Sconet, <a href='".$_SERVER['PHP_SELF']."?synchroniser=y".add_token_in_url()."'>cliquez ici</a>.</p>\n";
	}
	elseif(getSettingValue('mode_email_resp')=='mon_compte') {
		echo "<p>Pour mettre à jour les email des responsables d'après les valeurs des comptes d'utilisateurs, <a href='".$_SERVER['PHP_SELF']."?synchroniser=y".add_token_in_url()."'>cliquez ici</a>.</p>\n";
	}
	elseif(getSettingValue('mode_email_resp')=='sso') {
		echo "<p style='color:red'>Situation non encore gérée.</p>\n";
	}

	if($_SESSION['statut']=='administrateur') {
		echo "<p>Ce paramétrage peut être modifié dans <a href='../gestion/param_gen.php#mode_email_resp'>Configuration générale</a></p>\n";
	}

	echo "<p><br /></p>\n";
	require("../lib/footer.inc.php");
?>
