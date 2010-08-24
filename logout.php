<?php
/*
 * $Id$
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
$temp_perso="temp/".get_user_temp_directory();

if ($session_gepi->current_auth_mode == "sso" and $session_gepi->auth_sso == "cas") {
	$session_gepi->close(0);
    $session_gepi->logout_cas();
// On efface le dossier temporaire
foreach (glob($temp_perso."/*.*") as $filename) {
	if (is_file($filename) && (!strstr($filename, 'index.html'))){
    @unlink ($filename);
  }
}
unset ($filename);

    die();
}

// Ajout pour le multisite
if (isset($_COOKIE["RNE"])) {
	unset($_COOKIE['RNE']);
	setcookie('RNE', 'RNE'); // permet d'effacer le contenu du cookie.
}

	
    //$message = "<h1 class='gepi'>Déconnexion</h1>";
    $titre= "Déconnexion";
    $message = "";
	//$message .= "<img src='$gepiPath/images/icons/lock-open.png' alt='lock-open' /><br/><br/>";
    if (!$_GET['auto']) {
        $session_gepi->close($_GET['auto']);
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
           $message .= "A l'heure ci-dessous, une fenêtre GEPI s'est automatiquement fermée par mesure de sécurité car
           le temps maximum d'inactivité (".getSettingValue("sessionMaxLength")." minutes) avait été atteint.<br /><br />
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

include('./templates/origine/logout_template.php');


// On efface le dossier temporaire
foreach (glob($temp_perso."/*.*") as $filename) {
	if (is_file($filename) && (!strstr($filename, 'index.html'))){
    @unlink ($filename);
  }
}
unset ($filename);

?>
