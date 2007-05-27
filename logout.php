<?php
/*
 * Last modification  : 04/01/2006
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

if (isset($use_cas) and ($use_cas)) {
    require_once("./lib/cas.inc.php");
    // A ce stade, l'utilisateur est authentifié par CAS
    phpCAS::logout();
    die();
}

    $message = "<h1 class='gepi'>Déconnexion</h1>";
	$message .= "<img src='./images/icons/lock-open.png' alt='lock-open' /><br/><br/>";
    if (!$_GET['auto']) {
        closeSession($_GET['auto']);
        $message .= "Vous avez fermé votre session GEPI.<br />";
        $message .= "<a href=\"login.php\">Ouvrir une nouvelle session</a>.";
    } else if ($_GET['auto']==2) {
        closeSession($_GET['auto']);
        $message .= "Vous avez été déconnecté. Il peut s'agir d'une mauvaise configuration de la variable \$GepiPath dans la fichier \"connect.inc.php\"<br />
        <a href='aide_gepipath.php'><b>Aide à la configuration de \$GepiPath</b></a><br /><br />";
        $message .= "<a href=\"login.php\">Ouvrir une nouvelle session</a>.";
    } else if ($_GET['auto']==3) {
        $date_fermeture = date("d\/m\/Y\ \à\ H\ \h\ i");
        $debut_session = urldecode($_GET['debut_session']);
        $sql = "select now() > END TIMEOUT from log where SESSION_ID = '" . $_GET['sessionid'] . "' and START = '" . $debut_session . "'";
        if (sql_query1($sql)) {
           // Le temps d'inactivité est dépassé
           closeSession($_GET['auto']);
           $message .= "Votre session GEPI a expiré car le temps maximum (".getSettingValue("sessionMaxLength")." minutes) sans échange avec le serveur a été atteint.<br /><br />Date et heure de la déconnexion : ".$date_fermeture."<br /><br />";
           $message .= "<a href=\"login.php\">Ouvrir une nouvelle session</a>.";
        } else {
           $message .= "<h1 class='gepi'>Fermeture d'une fenêtre GEPI</h1>";
           $message .= "A l'heure ci-dessous, une fenêtre GEPI s'est automatiquement fermée par mesure de sécurité car
           le temps maximum d'inactivité (".getSettingValue("sessionMaxLength")." minutes) avait été atteint.<br /><br />
           Heure et date de fermeture de la fenêtre : ".$date_fermeture;
        }
    } else {
        closeSession($_GET['auto']);
        $message .= "Votre session GEPI a expiré, ou bien vous avez été déconnecté.<br />";
        if ((getSettingValue("disable_login"))=='yes') $message .=  "<br /><font color=\"red\" size=\"+1\">Le site est momentanément inaccessible. Veuillez nous excuser de ce dérangement !</font><br /><br />";
        $message .= "<a href=\"login.php\">Ouvrir une nouvelle session</a>.";
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<META HTTP-EQUIV="Pragma" CONTENT="no-cache" />
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache" />
<META HTTP-EQUIV="Expires" CONTENT="0" />
<title>Déconnexion</title>
<link rel="stylesheet" type="text/css" href="./<?php echo getSettingValue("gepi_stylesheet");?>.css" />
<link rel="shortcut icon" type="image/x-icon" href="./favicon.ico" />
<link rel="icon" type="image/ico" href="./favicon.ico" />
<?php
	// Styles paramétrables depuis l'interface:
	if($style_screen_ajout=='y'){
		// La variable $style_screen_ajout se paramètre dans le /lib/global.inc
		// C'est une sécurité... il suffit de passer la variable à 'n' pour désactiver ce fichier CSS et éventuellement rétablir un accès après avoir imposé une couleur noire sur noire
		echo "<link rel='stylesheet' type='text/css' href='$gepiPath/style_screen_ajout.css' />";
	}
?>
</head>
<body>
<div class="center">
<?php
echo $message;

$agent = $_SERVER['HTTP_USER_AGENT'];

if (eregi("msie",$agent) && !eregi("opera",$agent)) {
	echo "<div style='width: 70%; margin: auto;'>";
	echo "<p><b>Note aux utilisateurs de Microsoft Internet Explorer :</b>";
	echo "<br/>Si vous subissez des déconnexions intempestives, si vous n'arrivez pas à vous connecter à Gepi, " .
			"ou bien s'il vous faut répéter plusieurs fois la procédure de connexion avant de pouvoir accéder aux outils de Gepi, " .
			"il est possible que votre navigateur en soit la cause. Nous vous recommandons de télécharger gratuitement et d'installer <a href='http://www.mozilla-europe.org/fr/products/firefox/'>Mozilla Firefox</a>, " .
			"qui vous garantira les meilleures conditions d'utilisation de Gepi.</p>";
	echo "</div>";
}

?>
</div>
</body>
</html>