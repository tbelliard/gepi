<?php
/*
 * @version: $Id$
 *
 * Copyright 2001, 2015 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

$accessibilite="y";
$titre_page = "Gestion du module modèle Open Office";
$niveau_arbo = 1;
$gepiPathJava="./..";
$post_reussi=FALSE;
$msg = '';

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

// Check access
if (!checkAccess()) {
  header("Location: ../logout.php?auto=1");
  die();
}

//INSERT INTO droits VALUES ( '/mod_ooo/ooo_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Modèle Ooo : Admin', '');
//$tab_req[] = "INSERT INTO droits VALUES ( '/mod_ooo/ooo_admin.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Modèle Ooo : Admin', '');";

$msg = '';
if ((isset($_POST['is_posted']))&&(isset($_POST['activer']))) {
	check_token();
	if (!saveSetting("active_mod_ooo", $_POST['activer'])) $msg.= "Erreur lors de l'enregistrement du paramètre activation/désactivation !";

	if (isset($_POST['fb_dezip_ooo'])) {
		if (!saveSetting("fb_dezip_ooo", $_POST['fb_dezip_ooo'])) {
			$msg .= "Erreur lors de l'enregistrement de fb_dezip_ooo !";
		}
	}
}

if ((isset($_POST['is_posted']))&&($_POST['is_posted']==2)) {
	check_token();

	if (isset($_POST['OOoUploadProf'])) {
		$value="yes";
	}
	else {
		$value="no";
	}
	if (!saveSetting("OOoUploadProf", $value)) {
		$msg .= "Erreur lors de l'enregistrement de OOoUploadProf !";
	}

	if (isset($_POST['OOoUploadCpe'])) {
		$value="yes";
	}
	else {
		$value="no";
	}
	if (!saveSetting("OOoUploadCpe", $value)) {
		$msg .= "Erreur lors de l'enregistrement de OOoUploadCpe !";
	}

	if (isset($_POST['OOoUploadScol'])) {
		$value="yes";
	}
	else {
		$value="no";
	}
	if (!saveSetting("OOoUploadScol", $value)) {
		$msg .= "Erreur lors de l'enregistrement de OOoUploadScol !";
	}

	$login_user=isset($_POST['login_user']) ? $_POST['login_user'] : array();
	$tab_autorise=array();
	$sql="SELECT login FROM preferences WHERE name='AccesOOoUpload' AND value LIKE 'y%';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			if(!in_array($lig->login, $login_user)) {
				$sql="DELETE FROM preferences WHERE name='AccesOOoUpload' AND login='".$lig->login."';";
				$del=mysqli_query($GLOBALS["mysqli"], $sql);
			}
			else {
				$tab_autorise[]=$lig->login;
			}
		}
	}

	for($loop=0;$loop<count($login_user);$loop++) {
		if(!in_array($login_user[$loop], $tab_autorise)) {
			$sql="INSERT INTO preferences SET name='AccesOOoUpload', value='y', login='".$login_user[$loop]."';";
			$insert=mysqli_query($GLOBALS["mysqli"], $sql);
		}
	}
}

if (isset($_POST['is_posted']) and ($msg=='')) {
	$msg.= "Les modifications ont été enregistrées !";
	$post_reussi=TRUE;
}
// header
//$titre_page = "Gestion du module modèle Open Office";





// ====== Inclusion des balises head et du bandeau =====
include_once("../lib/header_template.inc.php");

if (!suivi_ariane($_SERVER['PHP_SELF'],"Gestion modèle Open Office"))
		echo "erreur lors de la création du fil d'ariane";
/****************************************************************
			FIN HAUT DE PAGE
****************************************************************/

// ====== Vérification des répertoires =====
$nom_fichier_modele_ooo =''; //pour éviter un notice (la variable ne sert pas ici ..
$nom_dossier_modeles_ooo_mes_modeles="";
$droitRepertoire =array();
include_once ("./lib/chemin.inc.php");
// test d'écriture dans le dossier mes_modeles
$dossier_test = "./".$nom_dossier_modeles_ooo_mes_modeles."dossier_test";
@rmdir($dossier_test);
$resultat_mkdir = @mkdir($dossier_test);
if (!($resultat_mkdir)) {
  $droitRepertoire[]="ATTENTION : Les droits d'écriture sur le dossier
  /mod_ooo/$nom_dossier_modeles_ooo_mes_modeles sont incorrects. Gepi doit avoir les droits de création
  de dossiers et de fichiers dans ce dossier pour assurer le bon fonctionnement du module";
}
else {
	@rmdir($dossier_test);
}

$dossier_test = "./tmp/dossier_test";
@rmdir($dossier_test);
$resultat_mkdir = @mkdir($dossier_test);
if (!($resultat_mkdir)) {
  $droitRepertoire[]="ATTENTION : Les droits d'écriture sur le dossier /mod_ooo/tmp/ sont incorrects.
	Gepi doit avoir les droits de création de dossiers et de fichiers dans ce dossier pour assurer
	le bon fonctionnement du module";
}
else {
	@rmdir($dossier_test);
}





/****************************************************************
			BAS DE PAGE
****************************************************************/
$tbs_microtime	="";
$tbs_pmv="";
require_once ("../lib/footer_template.inc.php");

/****************************************************************
			On s'assure que le nom du gabarit est bien renseigné
****************************************************************/
if ((!isset($_SESSION['rep_gabarits'])) || (empty($_SESSION['rep_gabarits']))) {
	$_SESSION['rep_gabarits']="origine";
}

//==================================
// Décommenter la ligne ci-dessous pour afficher les variables $_GET, $_POST, $_SESSION et $_SERVER pour DEBUG:
// $affiche_debug=debug_var();


$nom_gabarit = '../templates/'.$_SESSION['rep_gabarits'].'/mod_ooo/ooo_admin_template.php';

$tbs_last_connection=""; // On n'affiche pas les dernières connexions
include($nom_gabarit);


?>
