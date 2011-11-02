<?php
/* $Id: login_sso.php 7827 2011-08-19 10:28:36Z dblanqui $
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

//test version de php
if (version_compare(PHP_VERSION, '5') < 0) {
    die('GEPI nécessite PHP5 pour fonctionner');
}

// Pour le multisite
if (isset($_GET["rne"])) {
	setcookie('RNE', $_GET["rne"], null, '/');
}

$niveau_arbo = 0;
$prevent_session_init = true; // On bloque l'initialisation automatique de la session.

// Cas particulier du single sign-out CAS
// On doit empêcher le filtrage de $_POST['logoutRequest'], qui contient des
// caractères spéciaux
if (isset($_POST) && array_key_exists('logoutRequest', $_POST)) {
    $logout_request = $_POST['logoutRequest'];
}
// Initialisations files
require_once("./lib/initialisations.inc.php");
include("./lib/initialisationsPropel.inc.php");

$auth_sso = in_array($gepiSettings['auth_sso'], array("lemon", "cas", "lcs"));

if ($auth_sso && isset($logout_request)) {
    $_POST['logoutRequest'] = $logout_request;
}

# Cette page a pour vocation de gérer les authentification SSO.
# Si l'authentification SSO n'est pas paramétrée, on renvoie tout de suite
# vers la page de login classique.

if (!$auth_sso) {
	session_write_close();
	header("Location:login.php");
	die();
}

// Authentification CAS : la session doit être gérée par phpCAS directement
// Il est donc indispensable de placer toute l'initialisation ici, et
// d'instancier la classe 'Session' sans initialiser la session php, qui
// sera déjà initialisée.
if ($gepiSettings['auth_sso'] == 'cas') {
		include_once('./lib/CAS.php');
		if ($mode_debug) {
		    phpCAS::setDebug($debug_log_file);
    }
		// config_cas.inc.php est le fichier d'informations de connexions au serveur cas
		$path = "./secure/config_cas.inc.php";
		include($path);

		# On défini l'URL de base, pour que phpCAS ne se trompe pas dans la génération
		# de l'adresse de retour vers le service (attention, requiert patchage manuel
		# de phpCAS !!)
		if (isset($gepiBaseUrl)) {
			$url_base = $gepiBaseUrl;
		} else {
			$url_base = Session::https_request() ? 'https' : 'http';
			$url_base .= '://';
			$url_base .= $_SERVER['SERVER_NAME'];
		}

    // La session doit être nommée de la même manière dans Session.class.php
    // sinon ça ne marchera pas...
    session_name("GEPI");
    
		// Le premier argument est la version du protocole CAS
		// Le dernier argument a été ajouté par patchage manuel de phpCAS.
		phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_root, true, $url_base);
		phpCAS::setLang('french');

		// redirige vers le serveur d'authentification si aucun utilisateur authentifié n'a
		// été trouvé par le client CAS.
		phpCAS::setNoCasServerValidation();

    // On a une demande de logout envoyée par le serveur CAS :
    //   il faut initialiser la session tout de suite, pour pouvoir la détruire complètement
    if (isset($logout_request)) {
      $session_gepi = new Session();
  		// Gestion du single sign-out
      phpCAS::setSingleSignoutCallback(array($session_gepi, 'cas_logout_callback'));
		  phpCAS::handleLogoutRequests(false);
		}
		// Authentification
		phpCAS::forceAuthentication();
    
    // Initialisation de la session, avec blocage de l'initialisation de la
    // session php ainsi que des tests de timeout et update de logs,
    // car l'authentification CAS n'est pas encore validée côté Gepi !
    $session_gepi = new Session(true);
} else {
  $session_gepi = new Session();
}



# L'instance de Session permettant de gérer directement les authentifications
# SSO, on ne s'embête pas :
$auth = $session_gepi->authenticate();

if ($auth == "1") {
	# Authentification réussie
	session_write_close();
	header("Location:accueil.php");
	die();
} else {
	# Echec d'authentification.
	session_write_close();
	header("Location:login_failure.php?error=".$auth."&mode=sso");
	die();
}
?>
