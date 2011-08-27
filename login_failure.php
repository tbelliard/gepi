<?php
/* $Id$
*
* Copyright 2001, 2008 Thomas Belliard
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

$niveau_arbo = 0;

// Initialisations files
require_once("./lib/initialisations.inc.php");


# On arrive sur cette page en cas d'échec de la procédure d'authentification.
# Le principe est simple : on affiche un message d'erreur selon le code passé
# en URL, et on propose un lien vers le formulaire de login.

# On initialise les messages :

# Compte bloqué : trop de tentatives infructueuses.
if ($_GET['error'] == '2') {
	$message = 'Trop de tentatives de connexion infructueuses : votre compte est momentanément verrouillé.';

# IP liste noire
} elseif ($_GET['error'] == '3') {
	$message = 'Connexion impossible : vous tentez de vous connecter à partir d\'une adresse IP interdite.';

# Auth externe réussie, mais utilisateur 'inactif'
} elseif ($_GET['error'] == '4') {
	$message = 'Vous avez bien été identifié mais <b>votre compte a été désactivé</b>. Impossible de continuer. Veuillez signaler ce problème à l\'administrateur du site.';

# Auth externe réussie, mais inconsistence dans le mode d'authentification
} elseif ($_GET['error'] == '5') {
	$message = 'Vous avez bien été identifié mais votre compte utilisateur est paramétré pour utiliser un autre mode d\'authentification. Si vous pensez qu\'il s\'agit d\'une erreur, veuillez signaler ce problème à l\'administrateur du site.';
    if ($_GET['mode'] == "sso" && ($session_gepi->auth_locale || $block_sso)) {
		$message .= "<br /><br />Si vous possédez un compte local d'accès à GEPI, vous pouvez néanmoins <b><a href='./login.php'>accéder à la page de connexion de GEPI</a></b>.";
    }
# Auth externe réussie, mais compte inexistant en local et import impossible
} elseif ($_GET['error'] == '6') {
	$message = 'Vous avez bien été identifié mais la mise à jour de votre profil dans GEPI n\'a pas pu s\'effectuer correctement. Impossible de continuer. Veuillez signaler ce problème à l\'administrateur du site.';

# L'administrateur a désactivé les connexions à Gepi
} elseif ($_GET['error'] == '7') {
	$message = 'GEPI est momentanément inaccessible.';

# Multisite : impossible de déterminer le RNE
} elseif ($_GET['error'] == '8') {
	$message = 'Vous avez été correctement authentifié, mais votre compte n\'a pas pu être associé avec un établissement configuré sur ce Gepi. Impossible de continuer. Veuillez signaler ce problème à l\'administrateur du site.';

# Simple échec de l'authentification.
} elseif ($_GET['error'] == '9') {
	$message = 'Vous n\'avez pas été authentifié. Vérifiez votre login/mot de passe.';

// Quand un statut 'autre' n'a pas de statut personnalisé
} elseif ($_GET['error'] == '10') {
	$message = 'Vous n\'avez pas de droits suffisants pour entrer, veuillez contacter l\'administrateur de Gepi.';

} elseif ($_GET['error'] == '11') {
	$message = 'Vous avez bien été identifié mais votre compte Gépi ne semble pas configuré correctement pour le SSO avec table . Contactez votre administrateur GEPI pour corriger le problème ';

} else {
	$message = 'Une erreur indéterminée s\'est produite lors de votre authentification.';
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Cache-Control" content="no-cache" />
<meta http-equiv="Expires" content="0" />
<title>Échec de la connexion à Gepi</title>
<link rel="stylesheet" type="text/css" href="./<?php echo getSettingValue("gepi_stylesheet");?>.css" />
<link rel="shortcut icon" type="image/x-icon" href="./favicon.ico" />
<link rel="icon" type="image/ico" href="./favicon.ico" />
<?php
	// Styles paramétrables depuis l'interface:
	//if($style_screen_ajout=='y'){
	if(($style_screen_ajout=='y')&&(file_exists('./style_screen_ajout.css'))) {
		// La variable $style_screen_ajout se paramètre dans le /lib/global.inc
		// C'est une sécurité... il suffit de passer la variable à 'n' pour désactiver ce fichier CSS et éventuellement rétablir un accès après avoir imposé une couleur noire sur noire
		echo "<link rel='stylesheet' type='text/css' href='$gepiPath/style_screen_ajout.css' />";
	}
?>
</head>
<body>
<div class="center">
<h1>Échec de la connexion à Gepi</h1>
<p style="color: red;">
<?php
echo $message;
?>
</p>
<p>
<?php
if (isset($_GET['mode']) && $_GET['mode'] == "sso") {
	echo "<a href='login_sso.php'>Retourner à la page d'authentification</a>";
	if ($session_gepi->auth_locale || $session_gepi->auth_ldap) {
		echo "</p><p><a href='login.php'>Accéder au formulaire d'authentification locale</a>";
	}
}elseif(isset($_GET['mode']) && $_GET['mode'] == "sso_table"){
    exit;
}else {
	echo "<a href='login.php'>Retourner à la page d'authentification</a>";
}
?>
</p>
</div>
</body>
</html>
