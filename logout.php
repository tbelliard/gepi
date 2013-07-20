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
$niveau_arbo = 0;
require_once("./lib/initialisations.inc.php");
global $gepiPath;

// On récupère le dossier temporaire pour l'effacer
if (isset($_SESSION['login'])){
  $temp_perso="temp/".get_user_temp_directory();
}else{
  $temp_perso=NULL;
}

$rne_courant="";
if(($multisite=='y')&&(isset($_COOKIE['RNE']))) {
	$rne_courant=$_COOKIE['RNE'];
}

if ($session_gepi->current_auth_mode == "sso" and $session_gepi->auth_sso == "cas") {
  $session_gepi->close(0);
  $session_gepi->logout_cas();
  // On efface le dossier temporaire
  if ($temp_perso){
	foreach (glob($temp_perso."/*") as $filename) {
	  if (is_file($filename) && (!strstr($filename, 'index.html'))) {
		@unlink ($filename);
		// 'signature' est un dossier et actuellement on ne supprime pas les dossiers au logout.
		// De la même façon, le dossier contenant les PDF d'archivage de bulletins n'est pas supprimé automatiquement.
		// Il faut passer par la Gestion des dossiers temporaires
	  }
	}
	unset ($filename);
  }
  die();
}

if (getSettingValue('gepiEnableIdpSaml20') == 'yes' && (!isset($_REQUEST['idploggedout']))) {
		include_once(dirname(__FILE__).'/lib/simplesaml/lib/_autoload.php');
		$auth = new SimpleSAML_Auth_GepiSimple();
		if ($auth->isAuthenticated()) {
			//on fait le logout de session avec simplesaml en tant que fournisseur d'identité. Ça va déconnecter uniqement les services associés.
			//Si gepi n'est pas connecté en local, il faut revenir à la page de logout et passer à la déconnexion de gepi 
			$logout_return_url = $_SERVER['REQUEST_URI'];
			if (strpos($logout_return_url, '?')) {
				$logout_return_url .= '&';
			} else {
				$logout_return_url .= '?';
			}
			$logout_return_url .= 'idploggedout=done';
			header("Location:./lib/simplesaml/www/saml2/idp/SingleLogoutService.php?ReturnTo=".urlencode($logout_return_url));
			exit();
		}
}
//print_r($session_gepi);die;

//$message = "<h1 class='gepi'>Déconnexion</h1>";
    $titre= "Déconnexion";
    $message = "";
    	
    if (!isset($_GET['auto']) || !$_GET['auto']) {
    	$session_gepi->close(0);
        $message .= "Vous avez fermé votre session GEPI.";
        //$message .= "<a href=\"$gepiPath/login.php\">Ouvrir une nouvelle session</a>.";
    } else if ($_GET['auto']==2) {
        $session_gepi->close($_GET['auto']);
        $message .= "Vous avez été déconnecté. Il peut s'agir d'une mauvaise configuration de la variable \$GepiPath dans le fichier \"connect.inc.php\"<br />
        <a href='aide_gepipath.php'><b>Aide à la configuration de \$GepiPath</b></a>";
        //$message .= "<a href=\"$gepiPath/login.php\">Ouvrir une nouvelle session</a>.";
    } else if ($_GET['auto']==3) {
        $date_fermeture = date("d\/m\/Y\ \à\ H\ \h\ i");
        $debut_session = urldecode($_GET['debut_session']);
        $sql = "select now() > END TIMEOUT from log where SESSION_ID = '" . $_GET['session_id'] . "' and START = '" . $debut_session . "'";
        if (sql_query1($sql)) {
           // Le temps d'inactivité est dépassé
           $session_gepi->close($_GET['auto']);
           $message .= "Votre session GEPI a expiré car le temps maximum (".getSettingValue("sessionMaxLength")." minutes) sans échange avec le serveur a été atteint.<br /><br />Date et heure de la déconnexion : ".$date_fermeture."";
           //$message .= "<a href=\"$gepiPath/login.php\">Ouvrir une nouvelle session</a>.";
        } else {
           $message .= "<h1 class='gepi'>Fermeture d'une fenêtre GEPI</h1>";
           $titre= "Fermeture d'une fenêtre GEPI";
           /*
			$message .= "A l'heure ci-dessous, une fenêtre GEPI s'est automatiquement fermée par mesure de sécurité car
           le temps maximum d'inactivité (".getSettingValue("sessionMaxLength")." minutes) avait été atteint.<br /><br />
           Heure et date de fermeture de la fenêtre : ".$date_fermeture;
           */
			$message .= "A l'heure ci-dessous, une fenêtre GEPI s'est automatiquement fermée par mesure de sécurité. Le temps maximum de ".getSettingValue("sessionMaxLength")." minutes sans échange avec le serveur a sans doute été atteint.<br /><br />
           Heure et date de fermeture de la fenêtre : ".$date_fermeture;
           //$message .= "<a href=\"$gepiPath/login.php\">Ouvrir une nouvelle session</a>.";
        }
    } else {
        $session_gepi->close($_GET['auto']);
        $message .= "Votre session GEPI a expiré, ou bien vous avez été déconnecté.<br />";
        if ((getSettingValue("disable_login"))=='yes')  {
        	$message .=  "<br /><span class=\"rouge gras\">Le site est momentanément inaccessible. Veuillez nous excuser de ce dérangement !<span>";
        }
        //$message .= "<a href=\"$gepiPath/login.php\">Ouvrir une nouvelle session</a>.";
    }

if(getSettingValue('temporary_dir_no_cleaning')!='yes') {
	// On efface le dossier temporaire
	if ($temp_perso) {
		foreach (glob($temp_perso."/*") as $filename) {
			if (is_file($filename) && (!strstr($filename, 'index.html'))) {
				// 'signature' est un dossier et actuellement on ne supprime pas les dossiers au logout.
				// De la même façon, le dossier contenant les PDF d'archivage de bulletins n'est pas supprimé automatiquement.
				// Il faut passer par la Gestion des dossiers temporaires
				@unlink ($filename);
			}
		}
	unset ($filename);
	}
}

// Ajout pour le multisite
unset($_COOKIE['RNE']);
setcookie('RNE', 'unset', null, '/'); // permet d'effacer le contenu du cookie.
include('./templates/origine/logout_template.php');


?>
