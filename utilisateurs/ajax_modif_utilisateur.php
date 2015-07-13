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

@set_time_limit(0);

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

$sql="SELECT 1=1 FROM droits WHERE id='/utilisateurs/ajax_modif_utilisateur.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/utilisateurs/ajax_modif_utilisateur.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Ajax : Modification utilisateur',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

check_token();

header('Content-Type: text/html; charset=utf-8');

$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : "");
$login_user=isset($_POST['login_user']) ? $_POST['login_user'] : (isset($_GET['login_user']) ? $_GET['login_user'] : "");
$auth_mode_user=isset($_POST['auth_mode_user']) ? $_POST['auth_mode_user'] : (isset($_GET['auth_mode_user']) ? $_GET['auth_mode_user'] : "");

/*
echo "\$mode=$mode<br />";
echo "\$login_user=$login_user<br />";
echo "\$auth_mode=$auth_mode<br />";
*/

//debug_var();

if($mode=='changer_auth_mode2') {
	//**************** EN-TETE *****************
	$titre_page = "Changer le auth_mode d'un compte";
	require_once("../lib/header.inc.php");
	//**************** FIN EN-TETE *****************

	echo "<p class='bold'><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

	if($login_user=='') {
		echo "<p style='color:red'>ERREUR&nbsp;: Aucun login n'a été transmis.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$sql="SELECT auth_mode, nom, prenom FROM utilisateurs WHERE login='$login_user';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p style='color:red'>ERREUR&nbsp;: Le compte $login_user n'existe pas.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	$lig_user=mysqli_fetch_object($res);
	$nom_user=$lig_user->nom;
	$prenom_user=$lig_user->prenom;
	$auth_mode_user=$lig_user->auth_mode;

	$tab_auth_mode=array('gepi', 'ldap', 'sso');
	echo "<form name='form_changer_auth_mode' id='form_changer_auth_mode' action ='ajax_modif_utilisateur.php' method='post'>\n";
	echo "<p>Modifier le auth_mode de $nom_user $prenom_user ($login_user)&nbsp;:</p>\n";
	echo "<input type='hidden' name='modif_sans_js' value='y' />\n";
	echo "<input type='hidden' name='mode' value='changer_auth_mode' />\n";
	echo "<input type='hidden' name='login_user' id='login_user' value='$login_user' />\n";
	for($loop=0;$loop<count($tab_auth_mode);$loop++) {
		echo "<input type='radio' name='auth_mode_user' id='auth_mode_user_$loop' value='".$tab_auth_mode[$loop]."' ";
		echo "/><label for='auth_mode_user_$loop'> $tab_auth_mode[$loop]</label><br />\n";
	}
	echo add_token_field();
	echo "<input type='submit' name='Valider' value='Valider' />\n";
	echo "</form>\n";

	require("../lib/footer.inc.php");
	die();
}
elseif($mode=='changer_auth_mode') {
	if(isset($_POST['modif_sans_js'])) {
		//**************** EN-TETE *****************
		$titre_page = "Changer le auth_mode d'un compte";
		require_once("../lib/header.inc.php");
		//**************** FIN EN-TETE *****************

		echo "<p class='bold'><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

		echo "<p>Modification auth_mode de $login_user&nbsp;: ";
	}

	$tab_auth_mode=array('gepi', 'ldap', 'sso');
	if(($login_user=='')||($auth_mode_user=='')||(!in_array($auth_mode_user,$tab_auth_mode))) {
		echo "<span style='color:red' title='Un des champs auth_mode_user ou login_user est incorrect.'> KO</span>";
		return false;
		die();
	}

	if($login_user==$_SESSION['login']) {
		echo "<span style='color:red' title='Changement auth_mode pour votre compte non autorisé.'> KO</span>";
		return false;
		die();
	}

	$sql="SELECT 1=1 FROM utilisateurs WHERE login='$login_user';";
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test)==0) {
		echo "<span style='color:red' title='$login_user non présent dans la table utilisateurs.'> KO</span>";
		return false;
		die();
	}

	$chaine_vidage_mdp="";
	if((($auth_mode_user=="ldap")||($auth_mode_user=="sso"))&&
	(!getSettingAOui('auth_sso_ne_pas_vider_MDP_gepi'))) {
		$sql="SELECT auth_mode FROM utilisateurs WHERE login='".$login_user."';";
		$res_old_auth_mode=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_old_auth_mode)>0) {
			$lig_old_auth_mode=mysqli_fetch_object($res_old_auth_mode);
			if($lig_old_auth_mode->auth_mode=="gepi") {
				$chaine_vidage_mdp=", password='', salt='', change_mdp='n' ";
			}
		}
	}

	$sql="UPDATE utilisateurs SET auth_mode='$auth_mode_user' $chaine_vidage_mdp WHERE login='$login_user';";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if($res) {
		if(($auth_mode_user=="sso")&&(getSettingAOui('sso_cas_table'))) {
			echo temoin_compte_sso($login_user);
		}
		else {
			echo "<span style='color:green;'>$auth_mode_user</span>";
		}
	}
	else {
		echo "<span style='color:red;' title=\"Erreur lors du changement auth_mode :
$sql\">ERREUR</span>";
	}

	if(isset($_POST['modif_sans_js'])) {
		echo "</p>";
		require("../lib/footer.inc.php");
		die();
	}
}
elseif($mode=="rendre_actif_ou_inactif") {
	//**************** EN-TETE *****************
	$titre_page = "Changer l'activation d'un compte";
	require_once("../lib/header.inc.php");
	//**************** FIN EN-TETE *****************

	echo "<p class='bold'><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

	if($login_user=='') {
		echo "<p style='color:red'>ERREUR&nbsp;: Aucun login n'a été transmis.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$sql="SELECT auth_mode, nom, prenom FROM utilisateurs WHERE login='$login_user';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p style='color:red'>ERREUR&nbsp;: Le compte $login_user n'existe pas.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	$lig_user=mysqli_fetch_object($res);
	$nom_user=$lig_user->nom;
	$prenom_user=$lig_user->prenom;
	$etat=$lig_user->etat;

	echo "<form name='form_changer_etat' id='form_changer_etat' action ='ajax_modif_utilisateur.php' method='post'>\n";
	echo "<p>Modifier l'état de $nom_user $prenom_user ($login_user)&nbsp;:<br />
Le compte est actuellement <strong>$etat</strong></p>\n";
	echo "<input type='hidden' name='modif_sans_js' value='y' />\n";
	echo "<input type='hidden' name='mode' value='changer_etat_user' />\n";
	if($etat=="actif") {
		echo "<input type='submit' name='rendre_inactif' value='Rendre inactif' />\n";
	}
	else {
		echo "<input type='submit' name='rendre_inactif' value='Rendre actif' />\n";
	}
	echo add_token_field();
	echo "</form>\n";

	require("../lib/footer.inc.php");
	die();
}
elseif($mode == "changer_etat_user") {
	if(isset($_POST['modif_sans_js'])) {
		//**************** EN-TETE *****************
		$titre_page = "Changer l'état d'un compte";
		require_once("../lib/header.inc.php");
		//**************** FIN EN-TETE *****************

		echo "<p class='bold'><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

		echo "<p>Modification de l'état de $login_user&nbsp;: ";
	}

	if($login_user==$_SESSION['login']) {
		echo "<span style='color:red' title='Changement auth_mode pour votre compte non autorisé.'> KO</span>";
		return false;
		die();
	}

	$sql="SELECT etat FROM utilisateurs WHERE login='$login_user';";
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test)==0) {
		echo "<span style='color:red' title='$login_user non présent dans la table utilisateurs.'> KO</span>";
		return false;
		die();
	}

	$lig=mysqli_fetch_object($test);
	if($lig->etat == "actif") {
		$etat="inactif";
	}
	else {
		$etat="actif";
	}
	$sql="UPDATE utilisateurs SET etat='$etat' WHERE login='$login_user';";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if($res) {
		//echo "<span style='color:green;'>$etat</span>";
		if($etat=='actif') {
			echo "<img src='../images/icons/buddy.png' width='16' height='16' title='Compte actif' />";
		}
		else {
			echo "<img src='../images/icons/buddy_no.png' width='16' height='16' title='Compte inactif' />";
		}
	}
	else {
		echo "<span style='color:red;' title=\"Erreur lors de l'état vers $etat :
$sql\">ERREUR</span>";
	}

	if(isset($_POST['modif_sans_js'])) {
		echo "</p>";
		require("../lib/footer.inc.php");
		die();
	}
}

?>
